<?php

namespace CodeDistortion\TagTools;

use CodeDistortion\RelCss\RelevantCss;
use CodeDistortion\TagTools\Internal\Css\AdditionalHtml;
use CodeDistortion\TagTools\Internal\Css\EmbedCss;
use CodeDistortion\TagTools\Internal\Css\LinkCss;
use CodeDistortion\TagTools\Internal\CssSourceInterface;
use CodeDistortion\TagTools\Internal\TagAbstract;
use CodeDistortion\TagTools\Internal\Traits\HasCanPrioritiseSourcesTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

/**
 * Manage the css sources that need to be added to the page.
 */
class TagCss extends TagAbstract
{
    use HasCanPrioritiseSourcesTrait;


    /**
     * Used to access the filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The location to store caches of pre-parsed css.
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * The sources to get the css from.
     *
     * @var CssSourceInterface[]
     */
    protected $sources = [];

    /**
     * The additional html (files or strings) to also use when looking for used css.
     *
     * @var AdditionalHtml[]
     */
    protected $htmlSources = [];

    /**
     * The alias registered for this class in Laravel.
     *
     * @var string
     */
    protected static $alias = 'TagCss';

    /**b
     * The directive registered for use in blade.
     *
     * @var string
     */
    protected static $bladeDirective = 'tagcss';


    /**
     * Constructor.
     *
     * @param Filesystem $filesystem Used to access the filesystem.
     * @param string     $cacheDir   The location to store caches of pre-parsed css.
     */
    public function __construct(Filesystem $filesystem, string $cacheDir)
    {
        $this->filesystem = $filesystem;
        $this->cacheDir = $cacheDir;
        $this->templateStub = '@TagCss-'.md5(uniqid((string) mt_rand(), true));
    }


    /**
     * Add a css url to link to.
     *
     * @param string $url The css file url.
     * @return LinkCss
     */
    public function link(string $url): LinkCss
    {
        $source = new LinkCss($url);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add css (either the path to a file or css-definitions) to embed.
     *
     * @param string $css Either the path to the css file, or a string of css-definitions.
     * @return EmbedCss
     */
    public function embed(string $css): EmbedCss
    {
        $source = new EmbedCss($css, $this->filesystem);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add additional html (either a file or string) to use when looking for used css.
     *
     * @param string $html Either the path to the html file, or an html string.
     * @return AdditionalHtml
     */
    public function additionalHtml(string $html): AdditionalHtml
    {
        $source = new AdditionalHtml($html, $this->filesystem);
        $this->htmlSources[$source->duplicationHash()] = $source;
        return $source;
    }


    /**
     * Replace the stubs with css-definitions.
     *
     * @param string  $html       The content to process.
     * @param boolean $addTimings Should the time this took be added?.
     * @return string
     * @throws FileNotFoundException Thrown when the file can't be loaded.
     */
    public function replaceStubs(
        string $html,
        bool $addTimings = true
    ): string {

        $pos = mb_strpos($html, $this->templateStub);
        if ($pos === false) {
            return $html;
        }

        $count = 20;
        do {
            // render the replacement css
            $leadingWhitespace = $this->determineLeadingWhitespace($html, $pos);
            $replacement = $this->render($html, $leadingWhitespace, $addTimings);

            // remove the leading whitespace from the first line
            $replacement = mb_substr($replacement, mb_strlen($leadingWhitespace));

            // replace the first stub
            $html = substr_replace($html, $replacement, $pos, strlen($this->templateStub));

            // look for the next
            $pos = mb_strpos($html, $this->templateStub);
        } while (($pos !== false) && ($count-- > 0));

        return $html;
    }

    /**
     * Generate the output to be injected.
     *
     * @param string  $html              The page that needs styling.
     * @param string  $leadingWhitespace The whitespace to add to the beginning of each line.
     * @param boolean $addTimings        Should the time this took be added?.
     * @return string
     * @throws FileNotFoundException Thrown when the file can't be loaded.
     */
    public function render(string $html, string $leadingWhitespace = '', bool $addTimings = false): string
    {

        $start = microtime(true);

        $renderedParts = $relevantCssParts = $relevantCssParts = [];
        $lastWasEmbedCss = false;
        $lastEmbedCssSettingsHash = null;
        foreach ($this->prioritisedSources() as $source) {

            if (($source instanceof EmbedCss) && (!$source->willSelfRender())) {

                // add the EmbedCss details to a RelevantCss for rendering below (after all the EmbedCss's have been
                // added)

                // add this embedCSS to the previous one, provided its settings are the same
                $indexes = array_keys($renderedParts);
                $index = end($indexes);
                $newEmbedCssSettingsHash = $source->getSettingsHash();
                if ((!$lastWasEmbedCss) || ($newEmbedCssSettingsHash != $lastEmbedCssSettingsHash)) {

                    $index++;
                    $renderedParts[$index] = '';
                    $relevantCssParts[$index] = $this->prepareNewRelevantCss(
                        $html,
                        $source->getRemoveUnused(),
                        $source->getMinify()
                    );

                    $lastWasEmbedCss = true;
                    $lastEmbedCssSettingsHash = $newEmbedCssSettingsHash;
                }
                $source->addToRelevantCss($relevantCssParts[$index]);

            } elseif ($source->willSelfRender()) {
                // add the LinkCss / EmbedCSS(raw)
                $renderedParts[] = $source->render($leadingWhitespace);
                $lastWasEmbedCss = false;
            }
        }

        // render the relevant-css parts
        foreach ($relevantCssParts as $index => $relevantCss) {
            /** @var RelevantCss $relevantCss */
            $renderedParts[$index] = $this->renderRelevantCss($relevantCss, $leadingWhitespace);
        }

        $return = implode(array_filter($renderedParts)); // stick them together

        // weed out the divider between adjacent <style>...</style> parts
        $return = str_replace(
            $leadingWhitespace.'</style>'.PHP_EOL.$leadingWhitespace.'<style>'.PHP_EOL,
            '',
            $return
        );

        // add the timing if necessary
        $end = microtime(true);
        if (($addTimings) && (mb_strlen($return))) {
            $return .= $leadingWhitespace.'<!-- '.round(($end - $start) * 1000).' ms -->'.PHP_EOL;
        }

        return $return;
    }

    /**
     * Build a new RelevantCss object.
     *
     * @param string  $html         The page that needs styling.
     * @param boolean $removeUnused Should unused css be removed?.
     * @param boolean $minify       Should the css be minified?.
     * @return RelevantCss
     */
    private function prepareNewRelevantCss(string $html, bool $removeUnused, bool $minify): RelevantCss
    {
        $relevantCss = RelevantCss::new($this->cacheDir)
                                  ->removeUnused($removeUnused)
                                  ->minify($minify)
                                  ->contentNeedsCss($html);

        foreach ($this->htmlSources as $source) {
            $source->addToRelevantCss($relevantCss);
        }
        return $relevantCss;
    }

    /**
     * Take the RelevantCss object and render it inside <style> tags.
     *
     * @param RelevantCss $relevantCss       The RelevantCss object to render.
     * @param string      $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    private function renderRelevantCss(
        RelevantCss $relevantCss,
        string $leadingWhitespace = ''
    ): string {

        $output = $relevantCss->render($leadingWhitespace.'    ');
        return (mb_strlen($output)
            ? $leadingWhitespace.'<style>'.PHP_EOL.$output.$leadingWhitespace.'</style>'.PHP_EOL
            : '');
    }
}
