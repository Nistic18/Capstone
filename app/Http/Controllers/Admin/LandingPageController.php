<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingPageContent;
use App\Models\LandingPageCard;

class LandingPageController extends Controller
{
    public function index()
    {
        $sections = LandingPageContent::all();
        return view('admin.landing.index', compact('sections'));
    }

    public function edit($section)
    {
        // Use firstOrCreate instead of firstOrFail to automatically create missing sections
        $content = LandingPageContent::firstOrCreate(
            ['section' => $section],
            [
                'title' => $this->getDefaultTitle($section),
                'content' => $this->getDefaultContent($section)
            ]
        );
        
        $cards = LandingPageCard::where('section', $section)->orderBy('order')->get();
        return view('admin.landing.edit', compact('content', 'cards'));
    }

    public function update(Request $request, $section)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'cards' => 'nullable|array',
            'cards.*.id' => 'nullable|exists:landing_page_cards,id',
            'cards.*.title' => 'nullable|string|max:255',
            'cards.*.content' => 'nullable|string',
            'cards.*.image' => 'nullable|image|max:2048',
            'cards.*.order' => 'nullable|integer',
            'cards.*.delete' => 'nullable|boolean',
            'cards.*.section' => 'nullable|string'
        ]);

        $content = LandingPageContent::firstOrCreate(['section' => $section]);

        // Update fields
        $content->title = $data['title'] ?? $content->title;
        $content->content = $data['content'] ?? $content->content;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('landing', 'public');
            $content->image = $path;
        }

        $content->save();

        // Update or create cards
        if ($request->has('cards')) {
            foreach ($request->cards as $id => $cardData) {
                // Handle deletion
                if (!empty($cardData['delete'])) {
                    LandingPageCard::find($id)?->delete();
                    continue;
                }

                // Handle new card
                if ($id === 'new') {
                    // Skip creating if all fields are empty
                    if (empty($cardData['title']) && empty($cardData['content'])) {
                        continue;
                    }

                    $card = new LandingPageCard();
                    $card->landing_page_content_id = $content->id;
                    $card->section = $cardData['section'] ?? $content->section;
                } else {
                    // Handle existing card
                    $card = LandingPageCard::find($id);
                    if (!$card) continue;
                }

                // Handle image upload
                if (isset($cardData['image']) && $cardData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $cardData['image']->store('landing/cards', 'public');
                    $card->image = $imagePath;
                }

                // Update card fields
                $card->title = $cardData['title'] ?? $card->title ?? null;
                $card->content = $cardData['content'] ?? $card->content ?? null;
                $card->order = $cardData['order'] ?? $card->order ?? 0;
                $card->save();
            }
        }

        return redirect()->route('admin.landing.index')->with('success', 'Landing page updated successfully!');
    }

    /**
     * Get default title for section
     */
    private function getDefaultTitle($section)
    {
        $defaults = [
            'hero' => '🐠 Fresh Fish Market',
            'about' => 'Why Choose Fish Market',
            'articles' => 'Latest Articles',
            'faq' => 'Frequently Asked Questions',
            'stores' => 'Our Locations',
            'contact' => 'Get In Touch',
            'cta' => 'Ready to Order Fresh Seafood?'
        ];

        return $defaults[$section] ?? ucfirst($section);
    }

    /**
     * Get default content for section
     */
    private function getDefaultContent($section)
    {
        $defaults = [
            'hero' => 'Discover the freshest seafood delivered straight to your door.',
            'about' => 'Quality seafood with unmatched service',
            'articles' => 'Learn more about seafood and healthy eating',
            'faq' => 'Everything you need to know',
            'stores' => 'Find a Fish Market store near you',
            'contact' => "We'd love to hear from you",
            'cta' => 'Join thousands of happy customers enjoying premium quality fish.'
        ];

        return $defaults[$section] ?? '';
    }
}