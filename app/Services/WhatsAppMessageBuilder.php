<?php

namespace App\Services;

class WhatsAppMessageBuilder
{
    /**
     * Build formatted WhatsApp URL with encoded message.
     *
     * @param string $phone - Nomor WA tujuan format 62xxx
     * @param string $storeName - Nama toko dari settings
     * @param array $cart - Array item keranjang [{name, variant, qty, price}]
     * @param array $buyer - Data pemesan {name, phone, address, notes}
     * @param string|null $template - Custom template pesan (nullable)
     * @return string - Full WhatsApp API URL
     */
    public static function build(
        string $phone,
        string $storeName,
        array $cart,
        array $buyer,
        ?string $template = null
    ): string {
        if ($template !== null && trim($template) !== '') {
            $message = self::buildFromTemplate($template, $storeName, $cart, $buyer);
        } else {
            $message = self::buildDefaultMessage($storeName, $cart, $buyer);
        }

        $encodedText = urlencode($message);

        return "https://api.whatsapp.com/send?phone={$phone}&text={$encodedText}";
    }

    /**
     * Build message using default structured format.
     */
    protected static function buildDefaultMessage(string $storeName, array $cart, array $buyer): string
    {
        $lines = [];

        // Header toko
        $lines[] = "🛒 *Pesanan Baru*";
        $lines[] = "Toko: *{$storeName}*";
        $lines[] = "";

        // Detail pesanan
        $lines[] = "📋 *Detail Pesanan:*";
        $lines[] = "─────────────────";

        $total = 0;
        foreach ($cart as $index => $item) {
            $no = $index + 1;
            $name = $item['name'] ?? '';
            $variant = $item['variant'] ?? null;
            $qty = $item['qty'] ?? 1;
            $price = $item['price'] ?? 0;
            $subtotal = $price * $qty;
            $total += $subtotal;

            $line = "{$no}. {$name}";
            if (!empty($variant)) {
                $line .= " ({$variant})";
            }
            $line .= "\n   {$qty} x Rp " . number_format($price, 0, ',', '.') . " = Rp " . number_format($subtotal, 0, ',', '.');
            $lines[] = $line;
        }

        // Separator
        $lines[] = "─────────────────";

        // Total estimasi
        $lines[] = "*Total Estimasi: Rp " . number_format($total, 0, ',', '.') . "*";
        $lines[] = "";

        // Data pemesan
        $lines[] = "👤 *Data Pemesan:*";
        $lines[] = "Nama: " . ($buyer['name'] ?? '-');
        $lines[] = "No. WA: " . ($buyer['phone'] ?? '-');
        $lines[] = "Alamat: " . ($buyer['address'] ?? '-');

        if (!empty($buyer['notes'])) {
            $lines[] = "Catatan: " . $buyer['notes'];
        }

        $lines[] = "";
        $lines[] = "Terima kasih! 🙏";

        return implode("\n", $lines);
    }

    /**
     * Build message from custom template.
     *
     * Supported placeholders:
     * {store_name} - Nama toko
     * {order_details} - Detail pesanan terformat
     * {total} - Total estimasi
     * {buyer_name} - Nama pemesan
     * {buyer_phone} - Nomor WA pemesan
     * {buyer_address} - Alamat pemesan
     * {buyer_notes} - Catatan pemesan
     */
    protected static function buildFromTemplate(string $template, string $storeName, array $cart, array $buyer): string
    {
        // Build order details string
        $orderLines = [];
        $total = 0;
        foreach ($cart as $index => $item) {
            $no = $index + 1;
            $name = $item['name'] ?? '';
            $variant = $item['variant'] ?? null;
            $qty = $item['qty'] ?? 1;
            $price = $item['price'] ?? 0;
            $subtotal = $price * $qty;
            $total += $subtotal;

            $line = "{$no}. {$name}";
            if (!empty($variant)) {
                $line .= " ({$variant})";
            }
            $line .= " - {$qty} x Rp " . number_format($price, 0, ',', '.') . " = Rp " . number_format($subtotal, 0, ',', '.');
            $orderLines[] = $line;
        }

        $orderDetails = implode("\n", $orderLines);
        $totalFormatted = "Rp " . number_format($total, 0, ',', '.');

        // Replace placeholders
        $message = str_replace(
            [
                '{store_name}',
                '{order_details}',
                '{total}',
                '{buyer_name}',
                '{buyer_phone}',
                '{buyer_address}',
                '{buyer_notes}',
            ],
            [
                $storeName,
                $orderDetails,
                $totalFormatted,
                $buyer['name'] ?? '-',
                $buyer['phone'] ?? '-',
                $buyer['address'] ?? '-',
                $buyer['notes'] ?? '',
            ],
            $template
        );

        return $message;
    }
}
