<?php
// Copy this file to config.local.php (same folder) and fill values.
// Never commit config.local.php.

// Choose provider: 'gemini' or 'openai'
// If omitted, the server will auto-pick Gemini when GEMINI_API_KEY is set, otherwise OpenAI.
// define('AI_PROVIDER', 'gemini');

// === Google Gemini ===
// Get a key from Google AI Studio / Google Cloud (Generative Language API).
// IMPORTANT: Put your real key only in config.local.php.
define('GEMINI_API_KEY', 'AIzaSyAQA5d3XLBOndq7D3vfTXhmV-ocCKVk4dM');

// Optional (requested model)
define('GEMINI_MODEL', 'gemini-2.5-flash-lite');

// === OpenAI (optional fallback) ===
// define('OPENAI_API_KEY', 'sk-your-openai-api-key-here');
// define('OPENAI_MODEL', 'gpt-4o-mini');
