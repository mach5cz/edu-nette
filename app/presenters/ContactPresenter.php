<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette\Database\Context;
use Nette\Application\UI;
use App\Forms;


class ContactPresenter extends BasePresenter
{
    /** @var Context */
    private $database;

    /** @var Forms\ContactFormFactory @inject */
    public $contactFormFactory;


    /**
     * ContactPresenter constructor.
     * @param Context $database
     */
    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    /**
     * Renders default contact page
     */
    public function renderDefault()
    {
        $this->database->table('contact_form')->wherePrimary(1)->fetch();
    }


    /**
     * Contact form component
     * @return mixed
     */
    protected function createComponentContactForm()
    {
        $form = $this->contactFormFactory->create();
        $form->onSuccess[] = function (UI\Form $form) {
            $this->flashMessage('Formulář byl úspěšně odeslán, děkujeme.');
            $this->redirect('this');
        };

        return $form;
    }

}
