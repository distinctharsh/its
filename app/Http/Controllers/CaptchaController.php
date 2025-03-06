<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function generateCaptcha()
    {
        // Fixed dimensions for CAPTCHA image
        $imageWidth = 120;
        $imageHeight = 40;

        // Path to the background image
        $backgroundImagePath = public_path('images/cap.png');

        // Create a blank image with fixed size
        $image = imagecreatetruecolor($imageWidth, $imageHeight);

        // Set the background color (white or transparent)
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $bgColor);

        // Load the background image
        if (file_exists($backgroundImagePath)) {
            $backgroundImage = imagecreatefrompng($backgroundImagePath);
            // Resample the background image to fit the CAPTCHA dimensions
            imagecopyresampled($image, $backgroundImage, 0, 0, 0, 0, $imageWidth, $imageHeight, imagesx($backgroundImage), imagesy($backgroundImage));
            imagedestroy($backgroundImage);
        } else {
            return response()->json(['error' => 'Background image not found.']);
        }

        // Set the text color
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text

        // Load the font file correctly
        $fontPath = public_path('fonts/monofont.ttf');

        if (!file_exists($fontPath)) {
            return response()->json(['error' => 'Font file not found.']);
        }

        // Generate random CAPTCHA text
        $text = substr(str_shuffle('ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789'), 0, 6);

        // Store CAPTCHA text in session
        session(['captcha_text' => $text]);

        // Add the text to the background image
        imagettftext($image, 20, 0, 10, 30, $textColor, $fontPath, $text);

        // Output the image
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }
}
