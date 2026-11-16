<?php
// Vercel serverless entrypoint. The actual application lives in the project
// root (kept there so local servers such as XAMPP can serve it directly).
// __DIR__ inside the required file still resolves to the project root,
// so all relative asset paths and filemtime() cache-busting keep working.
require __DIR__ . '/../index.php';
