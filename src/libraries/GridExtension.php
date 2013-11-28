<?php namespace Flatturtle\Sitecore;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;

class GridExtension implements ExtensionInterface {

	/**
     * {@inheritdoc}
     */
    public function register(\Ciconia\Markdown $markdown)
    {
        $markdown->on('inline', array($this, 'detectGrid'));
    }

    /**
     * @param Text $text
     */
    public function detectGrid(Text $text)
    {
    	// Search for grid-pattern
    	if ($text->match('/{\.(col-[^}]+)}/', $matches))
    	{
    		// Wrap with tags
    		$text->replace('{' . $matches[0] . '}', '');
    		$text->wrap('<div class="' . $matches[1] . '"><p>', '</p></div>');
    	}
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'grid';
    }

}
