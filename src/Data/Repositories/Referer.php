<?php

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Tracker\Support\RefererParser;

class Referer extends Repository
{
    /**
     * @var RefererParser
     */
    private $refererParser;

    /**
     * @var
     */
    private $currentUrl;

    /**
     * @var
     */
    private $searchTermModel;

    /**
     * Create repository instance.
     *
     * @param RefererParser $refererParser
     */
    public function __construct($model, $searchTermModel, $currentUrl, RefererParser $refererParser)
    {
        parent::__construct($model);

        $this->refererParser = $refererParser;

        $this->currentUrl = $currentUrl;

        $this->searchTermModel = $searchTermModel;
    }

    /**
     * @param $refererUrl
     * @param $host
     * @param $domain_id
     *
     * @return mixed
     */
    public function store($refererUrl, $host, $domain_id)
    {
        $attributes = [
            'url'               => $refererUrl,
            'host'              => $host,
            'domain_id'         => $domain_id,
            'medium'            => null,
            'source'            => null,
            'search_terms_hash' => null,
        ];

        $parsed = $this->refererParser->parse($refererUrl, $this->currentUrl);

        if ($parsed->isKnown()) {
            $attributes['medium'] = $parsed->getMedium();

            $attributes['source'] = $parsed->getSource();

            $attributes['search_terms_hash'] = sha1($parsed->getSearchTerm());
        }

        $referer = $this->findOrCreate(
            $attributes,
            ['url', 'search_terms_hash']
        );

        $referer = $this->find($referer);

        if ($parsed->isKnown()) {
            $this->storeSearchTerms($referer, $parsed);
        }

        return $referer->id;
    }

    private function storeSearchTerms($referer, $parsed)
    {
        foreach (explode(' ', $parsed->getSearchTerm()) as $term) {
            $this->findOrCreate(
                [
                    'referer_id'  => $referer->id,
                    'search_term' => $term,
                ],
                ['referer_id', 'search_term'],
                $created,
                $this->searchTermModel
            );
        }
    }
}
