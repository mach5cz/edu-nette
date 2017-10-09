<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI;
use Nette\Mail\IMailer;
use Nette\Mail\Message;


class ContactPresenter extends BasePresenter
{
    /**
     * @var IMailer
     */
    private $mailer;

    /**
     * ContactPresenter constructor.
     */
    public function __construct(IMailer $mailer)
    {
        $this->mailer = $mailer;
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


    /*
     * Proceeds Contact form
     * return void
     */
    public function contactFormSucceeded(UI\Form $form, $values)
    {
        $mail = ['customer' => NULL, 'admin' => NULL];

        // customer message
        $mail['customer'] = new Message;
        $mail['customer']->setFrom($this->context->parameters['mailFrom'])
            ->addTo($values->email)
            ->setSubject('Potvrzení')
            ->setBody("Dobrý den,\nděkujeme za Vaši zprávu.");

        // admin message
        $mail['admin'] = new Message;
        $mail['admin']->setFrom($this->context->parameters['mailFrom'])
            ->addTo($this->context->parameters['mailAdmin'])
            ->setSubject('Potvrzení')
            ->setBody("Dobrý den,\nna webu " . $this->context->parameters['projectTitle'] . " byla vyplněna následující zpráva" .
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
