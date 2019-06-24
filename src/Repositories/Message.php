<?php

namespace PragmaRX\Tracker\Repositories;

class Message
{
    /**
     * Saved messages.
     *
     * @var \Illuminate\Support\Collection
     */
    private $messageList;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->messageList = collect();
    }

    /**
     * Add a message to the messages list.
     *
     * @param $message
     *
     * @return void
     */
    public function addMessage($message)
    {
        collect((array) $message)->each(function ($item) {
            collect($item)->flatten()->each(function ($flattened) {
                $this->messageList->push($flattened);
            });
        });
    }

    /**
     * Get the messages.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMessages()
    {
        return $this->messageList;
    }
}
