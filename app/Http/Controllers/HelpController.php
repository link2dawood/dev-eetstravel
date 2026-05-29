<?php

namespace App\Http\Controllers;

use App\Http\Requests\HelpContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Help / Documentation surface — staff-facing.
 *
 * Routes:
 *   GET  /help          help.index   — renders the single Help index page
 *                                       (quick links + FAQ + contact form).
 *   POST /help/contact  help.contact — accepts a contact-form submission,
 *                                       validates it (HelpContactRequest),
 *                                       logs structured Log::info, flashes
 *                                       a toast, redirects back to /help.
 *
 * Auth + permission gating happens at the route layer (web + perm
 * middleware), seeded by database/seeds/PermissionsHelpSeeder.php.
 *
 * Per AUDIT.md Help section, this controller scaffolds a module that
 * did not previously exist. There is no business logic to preserve.
 */
class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * GET /help — single-page Help index with quick links, FAQ, contact form.
     *
     * Content is static (curated, not user-supplied) so it ships in the view.
     * Keeps the URL free from any user-controlled identifiers.
     */
    public function index(): View
    {
        return view('help.index', [
            'faqs'       => $this->faqs(),
            'quickLinks' => $this->quickLinks(),
        ]);
    }

    /**
     * POST /help/contact — receive a contact-form submission.
     *
     * No mail is sent yet (would require a new mail dependency + queue
     * config — flagged in PHASE3_help_PLAN.md for a follow-up PR).
     * Instead we log a structured Log::info record so the team can grep
     * /storage/logs for incoming requests today.
     */
    public function contact(HelpContactRequest $request): RedirectResponse
    {
        $user = Auth::user();

        Log::info('help.contact.submitted', [
            'user_id' => $user?->id,
            'name'    => $user?->name,
            'email'   => $user?->email,
            'subject' => $request->validated()['subject'],
            'length'  => mb_strlen($request->validated()['message']),
        ]);

        return redirect()
            ->route('help.index')
            ->with('toast', [
                'message' => 'Thanks — your message has been logged. We will follow up via email.',
                'variant' => 'success',
            ]);
    }

    /**
     * Curated quick-links rendered in the view. Each entry is:
     *   [icon (Lucide name), label, href, optional description]
     *
     * Kept here (not in the view) so the test can assert their presence
     * without coupling to markup.
     */
    private function quickLinks(): array
    {
        return [
            [
                'icon'        => 'book-open',
                'label'       => 'Tour management user guide',
                'href'        => 'https://github.com/link2dawood/dev-eetstravel#readme',
                'description' => 'Day-to-day workflows: creating tours, quotations, invoicing.',
            ],
            [
                'icon'        => 'keyboard',
                'label'       => 'Keyboard shortcuts',
                'href'        => '#faq-shortcuts',
                'description' => 'Common keyboard combinations across the dashboard.',
            ],
            [
                'icon'        => 'message-circle',
                'label'       => 'Report a bug or request a feature',
                'href'        => 'https://github.com/link2dawood/dev-eetstravel/issues/new',
                'description' => 'Opens a new GitHub issue with the bug-report template.',
            ],
            [
                'icon'        => 'shield-check',
                'label'       => 'Privacy & data handling',
                'href'        => '#faq-privacy',
                'description' => 'How client data is stored, who can see what, retention policy.',
            ],
        ];
    }

    /**
     * Six static FAQ entries — keys become DOM ids so quick-links above
     * can anchor straight to them (e.g. #faq-shortcuts).
     */
    private function faqs(): array
    {
        return [
            [
                'id'       => 'faq-getting-started',
                'question' => 'How do I create my first tour?',
                'answer'   => 'Open Tours from the sidebar, click "New tour" in the top right, fill in the name, dates, and pax, and save. The tour appears in your list immediately and you can start adding day-by-day services from the tour page.',
            ],
            [
                'id'       => 'faq-shortcuts',
                'question' => 'What keyboard shortcuts are available?',
                'answer'   => 'Most pages support: / to focus the search box, n to create a new record, Esc to close modals, ↑ / ↓ to navigate tables. Shortcuts that affect the current page are listed in its toolbar.',
            ],
            [
                'id'       => 'faq-permissions',
                'question' => 'I can\'t access a page — what gives?',
                'answer'   => 'Pages are gated by per-role permissions. If you see a 403, ask an admin to grant your role the permission for that route. The admin can do this from Users → Roles.',
            ],
            [
                'id'       => 'faq-sharing',
                'question' => 'How do I share an itinerary with a client?',
                'answer'   => 'Open the tour page and click the "Share landing page" action. The link includes a per-tour token, so clients without the link cannot enumerate other tours.',
            ],
            [
                'id'       => 'faq-privacy',
                'question' => 'Who can see my drafts?',
                'answer'   => 'Drafts are visible to you and to any user with the relevant module permission. They do not appear on any public surface (landing page, supplier portal) until you explicitly publish them.',
            ],
            [
                'id'       => 'faq-stuck',
                'question' => 'I\'m stuck — what should I do?',
                'answer'   => 'Use the contact form on this page to send your team a short description of what you were trying to do and what went wrong. Include the URL of the page and any error message you saw.',
            ],
        ];
    }
}
