<?php namespace Flatturtle\Sitecore;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class GridExtension implements ExtensionInterface {

    /**
     * {@inheritdoc}
     */
    public function register(\Ciconia\Markdown $markdown)
    {
        // Run on finalize after everything else has been parsed
        $markdown->on('finalize', array($this, 'detectGrid'));
    }

    /**
     * @param Text $text
     */
    public function detectGrid(Text $text)
    {
        // Split into parts
        $parts = $text->split('/\n{2,}/', PREG_SPLIT_NO_EMPTY);

        // Parse parts
        $parts->apply(function (Text $part)
        {
            // Search grid pattern
            if ($part->match('/{\.(col-[^}]+)}/', $matches))
            {
                // Wrap
                $part->replace('{' . $matches[0] . '}', '');
                $part->wrap('<div class="' . $matches[1] . '">', '</div>');
            }

            return $part;
        });

        // Join parts
        $text->setString($parts->join("\n\n"));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'grid';
    }

}
