<?php
namespace Tests\Unit;

use Tests\TestCase;

class ContactLabelRenderingTest extends TestCase
{
    public function test_contact_name_requirement_is_explicit_for_new_and_loaded_rows(): void
    {
        $hotelContacts = collect([(object)['count'=>1, 'full_name'=>'Sample', 'mobile_phone'=>'', 'work_phone'=>'', 'email'=>'']]);
        foreach ([false, true] as $required) {
            $html = view('component.hotel_contact_form', ['count'=>0, 'contact_name_required'=>$required])->render();
            $html .= view('component.get_hotel_contacts_form', ['hotelContacts'=>$hotelContacts, 'contact_name_required'=>$required])->render();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);
            $this->assertCount($required ? 2 : 0, $xpath->query('//input[@required]'));
            foreach ($xpath->query('//label[contains(@for,"contact_full_name")]') as $label) {
                $this->assertSame($required, str_contains($label->textContent, '*'));
            }
        }
    }

    public function test_loaded_and_new_contact_ids_are_unique_and_labels_match(): void
    {
        $hotelContacts = collect([0, 1])->map(fn ($count) => (object)[
            'count' => $count, 'full_name' => 'Sample contact',
            'mobile_phone' => '', 'work_phone' => '', 'email' => 'sample@example.test',
        ]);
        $html = view('component.get_hotel_contacts_form', compact('hotelContacts'))->render();
        $html .= view('component.hotel_contact_form', ['count' => 2])->render();
        $dom = new \DOMDocument();
        @$dom->loadHTML('<input id="contact_email">'.$html);
        $xpath = new \DOMXPath($dom);
        $labels = $xpath->query('//label[@for]');
        $this->assertCount(12, $labels);
        foreach ($labels as $label) {
            $id = $label->getAttribute('for');
            $targets = $xpath->query('//input[@id="'.$id.'"]');
            $this->assertCount(1, $targets);
            $this->assertStringStartsWith('contacts[', $targets->item(0)->getAttribute('name'));
        }
        $this->assertCount(1, $xpath->query('//input[@id="contact_email"]'));
        $this->assertCount(3, $xpath->query('//button[@type="button" and @aria-label="Remove contact"]'));
        $this->assertCount(0, $xpath->query('//*[@id="delete_contact_item"]'));
    }
}
