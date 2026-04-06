# aire

**AI-powered resume optimization.**

aire analyzes a target job role, rewrites your resume content to match the position, adapts terminology to the job's language, and reorders your experience by relevance. It generates an ATS-optimized PDF ready to download.

## Features

- **ATS optimization:** Rewrites resume content using the job description's exact terminology and keywords
- **Tone selection:** Corporate & formal, Creative & modern, or Startup-friendly writing styles
- **Multi-language output:** English, Portuguese, Spanish (full translation including dates)
- **PDF generation:** Clean, professional PDF output generated client-side with jsPDF
- **ATS score & keywords:** Shows compatibility score and matched keywords after optimization
- **Iterative refinement:** Regenerate or provide feedback to adjust the output without starting over
- **Input validation:** Minimum length checks prevent low-quality inputs and hallucinated outputs
- **Anti-hallucination rules:** Strict prompt instructions prevent the AI from inventing names, companies, metrics, or contact information
- **Copy as Markdown:** Export the optimized resume as formatted Markdown
- **Password protection:** Optional login gate (configurable, can be disabled)
- **View last result:** Re-open the last generated output without calling the API again
- **Try example:** Pre-built demo with generic data, zero API cost

## Architecture

```
Browser (a1.html)
    │
    ├── config.json          ← prompts, AI model config, password
    │
    └── /api/refine.php      ← server-side proxy (holds API key)
            │
            └── Anthropic API (Claude)
```

The API key never leaves the server. The frontend contains no credentials.

## File Structure

```
aire/
├── a1.html              # Main application (HTML + CSS + JS, single file)
├── config.json           # All prompts, AI configuration, and password
├── api/
│   └── refine.php        # Server-side API proxy for Anthropic Claude
└── README.md             # This file
```

## Setup

### Prerequisites

- Web hosting with PHP and cURL support (e.g., Hostinger, shared hosting)
- An Anthropic API key from [console.anthropic.com](https://console.anthropic.com)

### Installation

1. **Upload all files** to your web server, preserving the folder structure:

   ```
   public_html/your-folder/
   ├── a1.html
   ├── config.json
   └── api/
       └── refine.php
   ```

2. **Add your API key.** Open `api/refine.php` and replace the placeholder:

   ```php
   $API_KEY = 'sk-ant-PASTE-YOUR-REAL-KEY-HERE';
   ```

3. **Update CORS origins** in `api/refine.php` to match your domain:

   ```php
   $allowed_origins = [
       'https://yourdomain.com',
       'https://www.yourdomain.com'
   ];
   ```

4. **Set your password** in `config.json`:

   ```json
   "password": "your-password-here"
   ```

   Set to `null` to disable password protection:

   ```json
   "password": null
   ```

5. **Test** by opening `https://yourdomain.com/your-folder/a1.html`

## Configuration

All configuration is in `config.json`. No need to edit the HTML.

### AI Model

```json
"ai": {
  "provider": "Anthropic Claude",
  "model": "claude-sonnet-4-20250514",
  "modelDisplay": "Claude Sonnet 4",
  "maxTokens": 4096
}
```

- `model`: The API model identifier sent to Anthropic
- `modelDisplay`: Human-readable name shown in the AI disclaimer
- `maxTokens`: Maximum output length (increase if resumes get truncated)

### Prompts

All AI prompts are in `config.json` under `prompts`:

| Key | Purpose |
|-----|---------|
| `systemBase` | Base system prompt for first-time optimization |
| `systemFeedback` | System prompt when user provides iterative feedback |
| `strategy` | 8-step optimization strategy with anti-hallucination rules |
| `toneGuide` | Defines how each tone option changes the writing style |
| `languageRule` | Ensures full translation including dates and connectors |
| `formatRules` | Output section structure (CONTACT, SUMMARY, EXPERIENCE, etc.) |
| `responseFormat` | JSON schema the AI must return |

### Password

```json
"password": "your-password"    // set a string to enable
"password": null                // set to null to disable
```

## Design System

aire follows the **Blau Design System** tokens:

| Token | Value | Usage |
|-------|-------|-------|
| `--blue` | #0071C5 | Primary accent, buttons, highlights |
| `--text` | #1a1a1a | Body text |
| `--mid` | #555 | Secondary text |
| `--light` | #999 | Tertiary text, labels |
| `--border` | #e0e0e0 | Borders, dividers |
| `--bg` | #fff | Background |
| `--font-title` | Raleway | Headings |
| `--font` | Open Sans | Body text |

## PDF Output Format

The generated PDF follows a clean, ATS-friendly layout:

- **Name:** Bold, 16pt, left-aligned
- **Title:** Normal weight, 9pt, gray
- **Section headers:** Bold, 10.5pt, ALL CAPS (CONTACT, SUMMARY, EXPERIENCE, etc.)
- **Job titles:** Bold, 9.5pt
- **Company names:** Italic, 9pt
- **Dates:** Italic, 8.5pt, gray (lighter visual weight than titles)
- **Bullets:** Normal, 9pt, indented
- **Skills/Languages:** Normal, 9pt, comma or pipe-separated

No colors, no decorations, no icons. Pure ATS-readable text.

## Input Validation

aire validates inputs at three levels:

1. **Frontend (before API call):**
   - Resume: minimum 200 characters / 40 words
   - Job description: minimum 100 characters
   - Tone and language must be selected

2. **Prompt rules (AI-side):**
   - Never invent names, emails, companies, dates, metrics, or skills
   - If the resume is too vague, return score 0

3. **Response check (after API call):**
   - Score 0 triggers an error message instead of showing output
   - Truncated responses (max_tokens) show a specific error
   - JSON parse failures show a descriptive error

## Security

- API key stored only in `refine.php` on the server
- CORS restricted to configured domains
- Password stored in `config.json` (server-side, not in HTML)
- `robots: noindex, nofollow` meta tag prevents search indexing
- No localStorage or cookies used

## Browser Support

Tested on modern browsers (Chrome, Firefox, Safari, Edge). Requires JavaScript enabled. Responsive layout for mobile and tablet.

## Credits

aire v1.0 — AI artifact by [Saul Ossorio](https://saulossorio.com)

## License

Private project. All rights reserved.
