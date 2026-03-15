<?php
/**
 * Valida que un archivo de imagen sea un código QR válido (decodificable).
 * Si no está disponible un decodificador, solo valida que sea imagen válida.
 * @param string $ruta_archivo Ruta temporal del archivo subido (ej. $_FILES['x']['tmp_name'])
 * @return array ['valido' => bool, 'mensaje' => string]
 */
function validar_imagen_es_qr($ruta_archivo) {
    if (!file_exists($ruta_archivo)) {
        return ['valido' => false, 'mensaje' => 'Archivo no encontrado.'];
    }
    $info = @getimagesize($ruta_archivo);
    if ($info === false) {
        return ['valido' => false, 'mensaje' => 'El archivo no es una imagen válida (use JPG o PNG).'];
    }
    $mimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/pjpeg'];
    if (!in_array($info['mime'], $mimes, true)) {
        return ['valido' => false, 'mensaje' => 'Solo se permiten imágenes JPG o PNG.'];
    }
    // Intentar decodificar QR si existe el paquete khanamiryan
    $decoderClass = null;
    if (class_exists('QrReader')) {
        $decoderClass = 'QrReader';
    } elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        @require_once __DIR__ . '/../vendor/autoload.php';
        if (class_exists('Khanamiryan\QrCodeReader\QrReader')) {
            $decoderClass = 'Khanamiryan\QrCodeReader\QrReader';
        }
    }
    if ($decoderClass !== null) {
        try {
            $reader = $decoderClass === 'QrReader'
                ? new QrReader($ruta_archivo)
                : new \Khanamiryan\QrCodeReader\QrReader($ruta_archivo);
            $text = method_exists($reader, 'text') ? $reader->text() : null;
            if ($text === null || trim((string)$text) === '') {
                return ['valido' => false, 'mensaje' => 'La imagen no es un código QR válido. Sube una imagen que contenga un código QR (por ejemplo, captura del QR de Nequi).'];
            }
            return ['valido' => true, 'mensaje' => 'Código QR válido.'];
        } catch (Throwable $e) {
            return ['valido' => false, 'mensaje' => 'La imagen no es un código QR válido o no se pudo leer.'];
        }
    }
    // Sin decodificador: solo comprobar que sea imagen (ya hecho) y opcionalmente que sea cuadrada
    $w = (int)($info[0] ?? 0);
    $h = (int)($info[1] ?? 0);
    if ($w > 0 && $h > 0 && abs($w - $h) > max($w, $h) * 0.5) {
        // Muy rectangular podría no ser QR típico; solo avisamos pero permitimos
    }
    return ['valido' => true, 'mensaje' => 'Imagen aceptada. Asegúrese de que sea una imagen del código QR de Nequi.'];
}
