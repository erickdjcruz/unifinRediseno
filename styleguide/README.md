# Mango styleguide

<sub>_Note_: This file is currently under construction and has started during the 13.3.0 release with the addition of
Tailwind CSS.</sub>

So you want to use CSS, huh? Well, you've come to the right place!  This is an unofficial™ styleguide for the Mango 
project. The contents are as follows.

#### Table of contents

* [File structure and patterns](#file-structure-and-patterns)
  * [Important files](#important-files)
* [Best practices](#best-practices)
* [Main technologies](#main-technologies)
  * [less/php](#lessphp)
  * [CSS variables](#css-variables)
  * [Maple Syrup](#maple-syrup)
  * [Tailwind CSS](#tailwind-css)
  * [SugarIcons](#sugaricons)


## File structure and patterns

`less/php` allows all `.less` files to be compiled into one larger CSS file to be loaded in the browser. As far as
the current build, it picks up files and changes from a few different places:

* `styleguide/assets` holds static files and assets such as fonts, images, and vendored styles
* `styleguide/less` holds all the LESS files to be compiled for the core application styles
  * The folder structure here is pretty hap-hazard and could use some love and a refactor

These folders are not maintained and their fate will be determined in an upcoming release:
* `styleguide/content` is fairly outdated and should not be referenced (call for deprecation)
* `styleguide/themes` which is an outdated platform for theming Sugar, do not use

### Important files

* `sugarcrm/styleguide/fixed_variables.less`: the primary entry point for less/php. This file houses most, if not all, variables
used in `styleguide/`
* `sugarcrm/styleguide/clients/base/sugar.less`: responsible for pulling in the rest of the styles within `styleguide/less`
* `sugarcrm/include/SugarTheme/SidecarTheme.php`: uses less/php to locate, compile, and prepare LESS files for consuming 
on the client side
* `sugarcrm/tailwind.css`: the entry point for Tailwind CSS for Mango. This file imports base libraries, and provides 
an area for customizations
* `sugarcrm/styleguide/less/sugar/sugar-theme-variables.less`: our CSS variable declaration for light/dark mode
* `sugarcrm/styleguide/less/sugar/utilities.less`: generic utilities for styling in Sugar. Slowly be reduced as a 
dependency with the addition of Tailwind. Avoid using utilities defined in that file, unless there isn't a Tailwind 
equivalent

## Best practices

## Main technologies

Mango utilizes a few core technologies for styling: 

* `less/php` is the official LESS compiler for PHP
* CSS variables that help for standardizing core styles (with light/dark mode support)
* Maple Syrup which holds our design tokens
* Tailwind CSS for all-purpose styling and utilities
* SugarIcons is our in-house icons library

Explore each subsection to learn more about each tool and how it's used. 

### less/php

LESS has been used at Sugar for some time. As of the initial draft of this document (28/09/2023), some of the frontend 
decisions have allowed us to reduce our dependency on LESS. Similar to native JavaScript, CSS has gotten a power up in 
the last couple of years that has added to its utility without the need for as many preprocessors and toolchains. 
Pair this with `less/php` not always being up-to-date with the latest version of `Less.js`, there isn't a strong need to 
rely on it so heavily as we once did. That being said, there are no plans to deprecate it, as it stills has hooks into 
our Sidecar Theme system.

LESS variables are used heavily within our code base. This was definitely a major feature many years ago, but as will be
discussed in a later section, native variable support exists. That being said, we still have a decent number of
variables that are used and managed in this codebase. 

TL;DR `less/php` is used to compile all of the `styleguide/less` folder in to a minified CSS file that gets pulled in on
the client side. 

<sub>More on themes and the `SidecarTheme` system later. This is WIP. - Scott King (28/09/2023)</sub>

### CSS variables

CSS variables is an awesome feature that counts for a majority of the reason for not needing a processor with CSS. They 
allow you to create global or scoped variables that can be used natively, and have
[great browser support](https://caniuse.com/css-variables). 

In Mango, we use them to handle generic, layout-level, styles that support light and dark modes. They are scoped 
by a top-level class, and inject the appropriate set of variables into the browser; which is not too different from a 
standard class declaration. We can use a single variable in code, ex. `var(--content-background)` and depending on the 
`body` element class that is used, as different value will be used. This is primarily used for colors at the moment, 
but any standard rule/value is supported. 

### Maple Syrup

Project location: [Maple Syrup on GitHub](https://github.com/sugarcrm/maple-syrup)

Now that we know how variables are loaded and used in the application, where do their values originate from? They come 
from a submodule of another project, called Maple Syrup. It is our internal design token system that is a library for 
atomic or subatomic properties for our products, that's also technology-agnostic. For example, a design token could be a 
color, text style, or measurement.

Maple Syrup is powered by AWS' [style-dictionary](https://github.com/amzn/style-dictionary) project. This platform 
allows design tokens to be defined using JSON, and then using transforms, they can be outputted to a variety of file
types and formats. 

As far as Mango development goes, the variables are used in a LESS format. The files are then imported in 
`styleguide/fixed_variables.less` and compiled. 

### Tailwind CSS

As you may be familiar, we have adopted a pattern of utility-based CSS instead of traditional semantic CSS. This often 
reduces a lot of complexity and duplication around styling the application. It also allows you to often separate the 
coupling of CSS and markup. Read more about the ideology 
[here](https://adamwathan.me/css-utility-classes-and-separation-of-concerns/). We have started adopting this approach 
[almost 2 years ago](https://github.com/sugarcrm/Mango/commit/1dd2488167b875906b0e6d3913e27357f8125b1c), at the time of 
initial draft. 

Tailwind CSS is a library that manages utility classes for us. It scans the files with a defined file criteria for 
classes that match the documented class names, and then adds them to a file that's rendered on the frontend. Two of
the best features it offers is: tree-shaking by default, and it's native CSS. This means, we can avoid being stuck with 
the less/php umbrella and can leverage whatever the browser is capable of... without the need for an additional 
preprocessor. Yes, Tailwind has its own config file and watch server for building/running locally, but all of this is 
handled through scripts defined in `package.json` (including tree-shaking and minification).

### SugarIcons

// TODO WIP
