import { put } from "@vercel/blob";

export default async function handler(req, res) {
    try {
        if (req.method !== "POST") {
            return res.status(405).json({
                success: false,
                message: "Method not allowed"
            });
        }

        const contentType =
            req.headers["content-type"] || "";

        if (!contentType.startsWith("application/json")) {
            return res.status(400).json({
                success: false,
                message: "Invalid request type"
            });
        }

        const body = req.body || {};

        const filename = body.filename;
        const base64 = body.data;
        const mimeType = body.mimeType;

        if (!filename || !base64 || !mimeType) {
            return res.status(400).json({
                success: false,
                message: "Image data missing"
            });
        }

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        if (!allowedTypes.includes(mimeType)) {
            return res.status(400).json({
                success: false,
                message: "Only JPG, PNG and WebP are allowed"
            });
        }

        const buffer = Buffer.from(base64, "base64");

        const maxSize = 40 * 1024 * 1024;

        if (buffer.length > maxSize) {
            return res.status(400).json({
                success: false,
                message: "Image size cannot exceed 40MB"
            });
        }

        const safeName =
            filename
                .replace(/[^a-zA-Z0-9._-]/g, "-")
                .replace(/-+/g, "-");

        const pathname =
            "news/" +
            Date.now() +
            "-" +
            Math.random().toString(36).substring(2, 10) +
            "-" +
            safeName;

        const blob = await put(
            pathname,
            buffer,
            {
                access: "public",
                contentType: mimeType,
                addRandomSuffix: false
            }
        );

        return res.status(200).json({
            success: true,
            url: blob.url,
            pathname: blob.pathname
        });

    } catch (error) {

        console.error("Blob upload error:", error);

        return res.status(500).json({
            success: false,
            message: "Image upload failed"
        });
    }
}
