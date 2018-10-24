<?php
namespace App\Library\Services;
use \Ddeboer\Imap\Server;
use \Ddeboer\Imap\SearchExpression;
use \Ddeboer\Imap\Search\Email\From;

class Mailbox
{
    public function __construct($url="", $username="", $password="")
    {	
    	$ifKey = \DotenvEditor::keyExists('IMAP_URL');
    	if (empty($url) & $ifKey) {
    		$keys = \DotenvEditor::getKeys();
    		$url = $keys["IMAP_URL"]["value"];
    		$username = $keys["IMAP_USERNAME"]["value"];
    		$password = $keys["IMAP_PASSWORD"]["value"];
    	} elseif(empty($url) & $ifKey) {
    		// lanzar error
    	} elseif(empty($url) & !$ifKey) {
    		$file = DotenvEditor::load();
            $file->setKey('IMAP_URL', $a["url"]);
            $file->setKey('IMAP_USERNAME', $a["username"]);
            $file->setKey('IMAP_PASSWORD', $a["password"]);
            $file->save();
    	}
    	$server = new Server($url);
    	$connection = $server->authenticate($username, $password);
        $this->conn = $connection;
    }
    public function searchFrom($mailbox, $criteria) {
        $search = new SearchExpression();
        $search->addCondition(new From($criteria));
        $mailbox = $this->conn->getMailbox($mailbox);
        $messages = $mailbox->getMessages($search);
        return $messages;
    }
    public function all($mailbox) {
        $mailbox = $this->conn->getMailbox($mailbox);
        $messages = $mailbox->getMessages();
        return $messages;
    }
    public function deleteAll() {
        $this->conn->expunge();
    }
}