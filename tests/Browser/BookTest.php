<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookTest extends DuskTestCase
{
    public function test_can_add_book()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('#addBookButton')
                    ->type('#title', 'Test Book')
                    ->type('#author', 'Test Author')
                    ->type('#genre', 'Fiction')
                    ->press('Add Book')
                    ->assertSee('Test Book');
        });
    }
}