<?php

/**
 * Helper class for phpMyFAQ FAQs.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ\Helper
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2010-2021 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2010-11-12
 */

namespace phpMyFAQ\Helper;

use Exception;
use ParsedownExtra;
use phpMyFAQ\Category;
use phpMyFAQ\Configuration;
use phpMyFAQ\Date;
use phpMyFAQ\Faq;
use phpMyFAQ\Helper;
use phpMyFAQ\Link;
use phpMyFAQ\Utils;

/**
 * Class FaqHelper
 *
 * @package phpMyFAQ\Helper
 */
class FaqHelper extends Helper
{
    /**
     * Constructor.
     *
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Rewrites the CSS class generated by TinyMCE for HighlightJS.
     *
     * @param string $answer
     * @return string
     */
    public function renderMarkupContent(string $answer): string
    {
        return str_replace('class="language-markup"', 'class="language-html"', $answer);
    }

    /**
     * Extends URL fragments (e.g. <a href="#foo">) with the full default URL.
     * @param string $answer
     * @param string $currentUrl
     * @return string
     */
    public function rewriteUrlFragments(string $answer, string $currentUrl): string
    {
        return str_replace('href="#', 'href="' . $currentUrl . '#', $answer);
    }

    /**
     * Renders a Share on Twitter link.
     *
     * @param  string $url
     * @return string
     */
    public function renderTwitterShareLink(string $url): string
    {
        if (empty($url) || $this->config->get('socialnetworks.disableAll')) {
            return '';
        }

        return sprintf(
            '<a rel="nofollow" href="%s" target="_blank"><i aria-hidden="true" class="fa fa-twitter"></i></a>',
            $url
        );
    }

    /**
     * Renders a "Send to friend" HTML snippet.
     *
     * @param  string $url
     * @return string
     */
    public function renderSendToFriend(string $url): string
    {
        if (empty($url) || !$this->config->get('main.enableSendToFriend')) {
            return '';
        }

        return sprintf(
            '<a rel="nofollow" href="%s"><i aria-hidden="true" class="fa fa-envelope"></i></a>',
            $url
        );
    }


    /**
     * Renders a select box with all translations of a FAQ.
     *
     * @param Faq $faq
     * @param int $categoryId
     * @return string
     */
    public function renderChangeLanguageSelector(Faq $faq, int $categoryId): string
    {
        global $languageCodes;

        $html = '';
        $faqUrl = sprintf(
            '?action=faq&amp;cat=%d&amp;id=%d&amp;artlang=%%s',
            $categoryId,
            $faq->faqRecord['id']
        );

        $oLink = new Link($this->config->getDefaultUrl() . $faqUrl, $this->config);
        $oLink->itemTitle = $faq->faqRecord['title'];
        $availableLanguages = $this->config->getLanguage()->languageAvailable($faq->faqRecord['id']);

        if (count($availableLanguages) > 1) {
            $html = '<form method="post">';
            $html .= '<select name="language" onchange="top.location.href = this.options[this.selectedIndex].value;">';

            foreach ($availableLanguages as $language) {
                $html .= sprintf('<option value="%s"', sprintf($oLink->toString(), $language));
                $html .= ($faq->faqRecord['lang'] === $language ? ' selected' : '');
                $html .= sprintf('>%s</option>', $languageCodes[strtoupper($language)]);
            }

            $html .= '</select></form>';
        }

        return $html;
    }

    /**
     * Renders a preview of the answer.
     *
     * @param string  $answer
     * @param int $numWords
     * @return string
     * @throws Exception
     */
    public function renderAnswerPreview(string $answer, int $numWords): string
    {
        if ($this->config->get('main.enableMarkdownEditor')) {
            $parseDown = new ParsedownExtra();
            return Utils::chopString(strip_tags($parseDown->text($answer)), $numWords);
        } else {
            return Utils::chopString(strip_tags($answer), $numWords);
        }
    }

    /**
     * Creates an overview with all categories with their FAQs.
     *
     * @param Category $category
     * @param Faq $faq
     * @param string $language
     * @return string
     * @throws Exception
     */
    public function createOverview(Category $category, Faq $faq, $language = ''): string
    {
        global $PMF_LANG;

        $output = '';

        // Initialize categories
        $category->transform(0);

        // Get all FAQs
        $faq->getAllRecords(FAQ_SORTING_TYPE_CATID_FAQID, ['lang' => $language]);
        $date = new Date($this->config);

        if (count($faq->faqRecords)) {
            $lastCategory = 0;
            foreach ($faq->faqRecords as $data) {
                if ($data['category_id'] !== $lastCategory) {
                    $output .= sprintf('<h3>%s</h3>', $category->getPath($data['category_id'], ' &raquo; '));
                }

                $output .= sprintf('<h4>%s</h4>', strip_tags($data['title']));
                $output .= sprintf('<article>%s</article>', $data['content']);
                $output .= sprintf(
                    '<p>%s: %s<br>%s',
                    $PMF_LANG['msgAuthor'],
                    $data['author'],
                    $PMF_LANG['msgLastUpdateArticle'] . $date->format($data['updated'])
                );
                $output .= '<hr>';

                $lastCategory = $data['category_id'];
            }
        }

        return $output;
    }

    /**
     * Creates a list of links with available languages to edit a FAQ
     * in the admin backend.
     *
     * @param  $faqId
     * @param  $faqLang
     * @return string
     */
    public function createFaqTranslationLinkList(int $faqId, string $faqLang): string
    {
        global $languageCodes;
        $output = '';

        $availableLanguages = $this->config->getLanguage()->languageAvailable(0, 'faqcategories');
        foreach ($availableLanguages as $languageCode) {
            if ($languageCode !== $faqLang) {
                $output .= sprintf(
                    '<a class="dropdown-item" href="?action=editentry&id=%d&translateTo=%s">%s %s</a>',
                    $faqId,
                    $languageCode,
                    'Translate to',
                    $languageCodes[strtoupper($languageCode)]
                );
            } else {
                $output .= '<a class="dropdown-item">n/a</a>';
            }
        }

        return $output;
    }
}
