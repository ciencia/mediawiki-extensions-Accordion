# Accordion
MediaWiki extension that generates an accordion with only HTML and CSS, without JavaScript.

**Warning** This extension is **experimental**

It was created as a test, and it's not currently being in use by the author.

Feel free to fork it and adapt to your needs.

# Usage

This extension works similar to [Tabber](https://www.mediawiki.org/wiki/Extension:Tabber).

Wrap the accordion contents between `<accordion></accordion>`, and separate each accordion section with `|-|` (this separator must be at the start of a line).

The title of each section will be the text before an equal sign, and the section contents will be everything after it.

Example:

```
<accordion>
Section name 1 = Section content 1
|-|
Section name 2 = Section content 2
|-|
Section name 3 = Section content 3
</accordion>
```

You can provide the following attributes to the `<accordion>` element:

* class: CSS class name for the entire accordion.
* style: CSS styles for the accordion element.
* activesection: Number of the section opened by default. First section is number 1.
