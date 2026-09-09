import { handleUpload } from "@vercel/blob/client";

export default async function handler(request) {
  try {
    if (request.method !== "POST") {
      return new Response(
        JSON.stringify({
          success: false,
          message: "Method Not Allowed"
        }),
        {
          status: 405,
          headers: {
            "Content-Type": "application/json"
          }
        }
      );
    }

    const body = await request.json();

    const response = await handleUpload({
      body,
      request,

      onBeforeGenerateToken: async () => {
        return {
          allowedContentTypes: [
            "image/jpeg",
            "image/png",
            "image/webp"
          ],

          maximumSizeInBytes: 40 * 1024 * 1024,

          addRandomSuffix: true
        };
      },

      onUploadCompleted: async ({ blob }) => {
        console.log("Vercel Blob upload completed:", blob.url);
      }
    });

    return Response.json(response);
  } catch (error) {
    console.error("Vercel Blob upload error:", error);

    return new Response(
      JSON.stringify({
        success: false,
        message: "Upload failed.",
        error: error?.message || "Unknown error"
      }),
      {
        status: 500,
        headers: {
          "Content-Type": "application/json"
        }
      }
    );
  }
}
