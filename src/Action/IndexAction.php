<?php
/**
 * Index Action Class
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2017 Bowling Green State University Libraries
 * @license MIT
 */

namespace App\Action;

use App\Exception\NotFoundException;
use TheIconic\Tracking\GoogleAnalytics\Analytics;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * A class to be invoked for the index action.
 */
class IndexAction
{
    /**
     * Object to send data to Google Analytics.
     * @var Analytics
     */
    protected $analytics;

    /**
     * Catalogs that can be redirected to.
     * @var array
     */
    protected $catalogs;

    /**
     * Construct the action with configuration.
     * @param Analytics $analytics Object to send data to Google Analytics.
     * @param string $catalogs Catalogs that can be redirected to.
     * @throws NotFoundException The link to redirect to is undefined.
     */
    public function __construct(Analytics $analytics, array $catalogs)
    {
        // Set the object to send data to Google Analytics.
        $this->analytics = $analytics;

        // Set the catalogs that can be redirected to.
        $this->catalogs = $catalogs;
    }

    /**
     * Method called when class is invoked as an action.
     * @param Request $req The request for the action.
     * @param Response $res The response from the action.
     * @param array $args The arguments for the action.
     * @return Response The response from the action.
     */
    public function __invoke(Request $req, Response $res, array $args)
    {
        // Determine which catalog to use.
        if (empty($args['catalog'])) {
            $catalog = reset($this->catalogs);
        } elseif (!empty($this->catalogs[$args['catalog']])) {
            $catalog = $this->catalogs[$args['catalog']];
        }

        if (empty($catalog)) {
            throw new NotFoundException(
                'Catalog is not defined: ' .
                $args['catalog']
            );
        }

        // Make sure a domain is specified for a catalog.
        if (empty($catalog['domain'])) {
            throw new NotFoundException('Catalog domain is not defined');
        }

        // Add the referrer to the Google Analytics hit if available.
        $referrer = $req->getHeader('HTTP_REFERER');

        if ($referrer) {
            $this->analytics->setDocumentReferrer(reset($referrer));
        }

        // Add the user agent to the Google Analytics hit if available.
        $userAgent =$req->getHeader('HTTP_USER_AGENT');

        if ($userAgent) {
            $this->analytics->setUserAgentOverride(reset($userAgent));
        }

        // Send the current URI to Google Analytics with the user's IP.
        $this->analytics
            ->setDocumentLocationUrl((string) $req->getUri())
            ->setIpOverride($req->getAttribute('ip_address'))
            ->sendPageview();

        // Build the URI to the catalog search.
        $uri = 'https://' . $catalog['domain'] . '/search';

        if (!empty($args['function'])) {
            $uri .= '/' . $args['function'];
        }

        $query = $req->getUri()->getQuery();

        if ($query) {
            $uri .= '?' . $query;
        }

        // Redirect to the catalog search.
        return $res->withStatus(302)->withHeader('Location', $uri);
    }
}
