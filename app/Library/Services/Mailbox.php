<?php
namespace App\Library\Services;
use \Ddeboer\Imap\Server;
use \Ddeboer\Imap\SearchExpression;
use \Ddeboer\Imap\Search\Email\From;

class Mailbox
{
    public function __construct($url=null, $username=null, $password=null)
    {	
    	$ifKey = \DotenvEditor::keyExists('IMAP_URL');
    	if (!$url and $ifKey) {
    		$keys = \DotenvEditor::getKeys();
    		$url = $keys["IMAP_URL"]["value"];
    		$username = $keys["IMAP_USERNAME"]["value"];
    		$password = $keys["IMAP_PASSWORD"]["value"];
    	} elseif(!$url and !$ifKey) {
    		throw new \Exception('Need to run firts the connect command');
    	} elseif($url and !$ifKey) {
    		$file = \DotenvEditor::load();
            $file = \DotenvEditor::setKeys([
                ['key'     => 'IMAP_URL', 'value'   => $url ],
                ['key'     => 'IMAP_USERNAME', 'value'   => $username ],
                ['key'     => 'IMAP_PASSWORD', 'value'   => $password ],
            ]);
            $file->save();
    	}
    	$server = new Server($url);
    	$connection = $server->authenticate($username, $password);
        $this->conn = $connection;
    }
    public function getConn() {
        return $this->conn;
    }
    public function searchFrom($mailbox, $criteria) {
        $search = new SearchExpression();
        $search->addCondition(new From($criteria));
        $mailbox = $this->conn->getMailbox($mailbox);
        $messages = $mailbox->getMessages($search);
        return $messages;
    }
    public function searchList($mailbox, $list) {
        $search = new SearchExpression();
        foreach ($list as $criteria) {
            $search->addCondition(new From($criteria));
        }
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