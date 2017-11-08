<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Misc\ConfigParameters;
use Nette\Application\UI;
use Nette\Database\Context;
use Nette\Mail\IMailer;
use Nette\Mail\Message;


class ContactPresenter extends BasePresenter
{
    /**
     * @var IMailer
     */
    private $mailer;

    /**
     * @var ConfigParameters
     */
    private $configParameters;

    /**
     * @var Context
     */
    private $database;


    /**
     * ContactPresenter constructor.
     * @param IMailer $mailer
     * @param ConfigParameters $configParameters
     * @param Context $database
     */
    public function __construct(IMailer $mailer, ConfigParameters $configParameters, Context $database)
    {
        $this->mailer = $mailer;
        $this->configParameters = $configParameters;
        $this->database = $database;
    }


    public function renderDefault()
    {
        $this->database->table('contact_form')->wherePrimary(1)->fetch();
    }


    /**
     * Creates Contact form
     * return UI\Form
     */
    protected function createComponentContactForm()
    {
        $form = new UI\Form;
        // bootstrap renderer
        $this->makeBootstrap3($form);

        $form->addText('email', 'E-mail:')->setRequired('Zadejte Váš e-mail');
        $form->addTextArea('message', 'Zpráva:');
        $form->addSubmit('login', 'Odeslat');
        $form->onSuccess[] = [$this, 'contactFormSucceeded'];

        return $form;
    }


    /**
     * Proceeds Contact form
     * @param UI\Form $form
     * @param $values
     * @throws \Nette\Application\AbortException
     */
    public function contactFormSucceeded(UI\Form $form, $values)
    {
        $mail = ['customer' => NULL, 'admin' => NULL];

        // customer message
        $mail['customer'] = new Message;
        $mail['customer']->setFrom($this->configParameters->getMailFrom())
            ->addTo($values->email)
            ->setSubject('Potvrzení')
            ->setBody("Dobrý den,\nděkujeme za Vaši zprávu.");

        // admin message
        $mail['admin'] = new Message;
        $mail['admin']->setFrom($this->configParameters->getMailFrom())
            ->addTo($this->configParameters->getMailAdmin())
            ->setSubject('Potvrzení')
            ->setBody("Dobrý den,\nna webu " . $this->configParameters->getProjectTitle() . " byla vyplněna následující zpráva" .
                "\n\nE-mail: " . $values->email .
                "\nZpráva: " . $values->message
            );

        // send mail
        foreach ($mail as $m) {
            $this->mailer->send($m);
        }

        $this->flashMessage('Formulář byl úspěšně odeslán, děkujeme.');
        $this->redirect('Contact:');
    }


}
