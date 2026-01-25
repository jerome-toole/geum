<?php
/**
 * Utility & Pattern Classes Style Guide
 */
?>
<style>
    .style-guide__section {
        max-width: 100%;
        padding: var(--space--base) 0;

        & > * {
            max-width: min(var(--container-sm), var(--width-fluid-container));
            margin-left: auto;
            margin-right: auto;

            &:where(.alignwide) {
                max-width: var(--width-fluid-lg);
            }

            &:where(.alignfull) {
                max-width: 100%;
            }

            &:where(.alignnone) {
                max-width: none;
            }
        }
    }
    .style-guide__section + .style-guide__section {
        border-top: 1px solid var(--color-border);
    }
    .style-guide__section h2 {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--color-foreground);
    }
    .style-guide__section h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .style-guide__subsection {
        margin-bottom: 2rem;
    }
    .style-guide__demo {
        padding: 1rem;
        border: 1px dashed var(--color-border);
        border-radius: var(--border-radius--s);
        margin-top: 0.75rem;
        background: var(--color-background);
    }
    .style-guide__demo--dark {
        background: var(--color-foreground);
        color: var(--color-background);
    }
    .style-guide__code {
        display: inline-block;
        padding: 0.15em 0.4em;
        border-radius: 3px;
        background: rgba(0,0,0,0.08);
        font-family: monospace;
        font-size: 0.85em;
    }
    .style-guide__box {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60px;
        padding: 0.5rem;
        border: 1px solid var(--color-border);
        background: var(--color-brand-2);
        text-align: center;
    }
    .style-guide__box--tall {
        min-height: 150px;
    }
    .style-guide__placeholder {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 80px;
        background: linear-gradient(135deg, var(--color-brand-2) 25%, var(--color-brand-3) 100%);
    }
</style>

<!-- ============================================
     PATTERNS (3-patterns)
     ============================================ -->

<section class="style-guide__section alignfull">
    <h2>Patterns</h2>

    <!-- Buttons -->
    <div class="style-guide__subsection">
        <h3>Buttons</h3>
        <small><code class="style-guide__code">.btn</code> and variants</small>
        <div class="style-guide__demo">
            <div class="flex-list">
                <button class="btn">.btn</button>
                <button class="btn btn--small">.btn--small</button>
                <button class="btn btn--label">.btn--label</button>
                <button class="btn btn--arrow">.btn--arrow</button>
                <button class="btn btn--square">1</button>
                <button class="btn btn--theme-2">.btn--theme-2</button>
            </div>
        </div>
    </div>

    <!-- Button Resets -->
    <div class="style-guide__subsection">
        <h3>Button Resets</h3>
        <small><code class="style-guide__code">.button-reset</code> <code class="style-guide__code">.button-reset-hard</code></small>
        <div class="style-guide__demo">
            <div class="flex-list">
                <button>Default button</button>
                <button class="button-reset">.button-reset</button>
                <button class="button-reset-hard">.button-reset-hard</button>
            </div>
        </div>
    </div>

    <!-- Links -->
    <div class="style-guide__subsection">
        <h3>Link Styles</h3>
        <small><code class="style-guide__code">.link</code> variants</small>
        <div class="style-guide__demo">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div><a href="#" class="link">.link (default)</a></div>
                <div><a href="#" class="link link--2">.link--2 (underline on hover)</a></div>
                <div><a href="#" class="link link--foreground">.link--foreground</a></div>
                <div><a href="#" class="link--multiline-background">.link--multiline-background (animated background fill)</a></div>
            </div>
        </div>
    </div>

    <!-- Typography -->
    <div class="style-guide__subsection">
        <h3>Typography</h3>
        <small><code class="style-guide__code">.type-*</code> styles</small>
        <div class="style-guide__demo">
            <div class="type-hero">.type-hero</div>
            <div class="type-h1">.type-h1</div>
            <div class="type-h2">.type-h2</div>
            <div class="type-h3">.type-h3</div>
            <div class="type-h4">.type-h4</div>
            <div class="type-h5">.type-h5</div>
            <div class="type-h6">.type-h6</div>
            <div class="type-base">.type-base</div>
            <div class="type-small">.type-small</div>
            <div class="type-meta">.type-meta</div>
        </div>
    </div>

    <!-- Grid -->
    <div class="style-guide__subsection">
        <h3>Grid</h3>
        <small><code class="style-guide__code">.grid-simple</code> <code class="style-guide__code">.grid-columns-*</code> <code class="style-guide__code">.grid-auto</code></small>
        <div class="style-guide__demo">
            <p style="margin-bottom: 0.5rem;"><strong>.grid-simple .sm:[--cols:2] .md:[--cols:3]</strong></p>
            <div class="grid-simple sm:[--cols:2] md:[--cols:3]">
                <div class="style-guide__box">1</div>
                <div class="style-guide__box">2</div>
                <div class="style-guide__box">3</div>
                <div class="style-guide__box">4</div>
                <div class="style-guide__box">5</div>
                <div class="style-guide__box">6</div>
            </div>
        </div>
        <div class="style-guide__demo">
            <p style="margin-bottom: 0.5rem;"><strong>.grid-auto .[--col-min-width:150px]</strong> (auto-fills based on min-width)</p>
            <div class="grid-simple grid-auto [--col-min-width:150px]">
                <div class="style-guide__box">Auto 1</div>
                <div class="style-guide__box">Auto 2</div>
                <div class="style-guide__box">Auto 3</div>
                <div class="style-guide__box">Auto 4</div>
            </div>
        </div>
    </div>

    <!-- Flex Grid -->
    <div class="style-guide__subsection">
        <h3>Flex Grid</h3>
        <small><code class="style-guide__code">.flex-grid</code> <code class="style-guide__code">.flex-grid-auto</code></small>
        <div class="style-guide__demo">
            <p style="margin-bottom: 0.5rem;"><strong>.flex-grid</strong> (uses --cols)</p>
            <div class="flex-grid" style="--cols: 4;">
                <div class="style-guide__box">1</div>
                <div class="style-guide__box">2</div>
                <div class="style-guide__box">3</div>
                <div class="style-guide__box">4</div>
            </div>
        </div>
    </div>

    <!-- Flex List -->
    <div class="style-guide__subsection">
        <h3>Flex List</h3>
        <small><code class="style-guide__code">.flex-list</code></small>
        <div class="style-guide__demo">
            <ul class="flex-list">
                <li class="style-guide__box" style="padding: 0.5rem 1rem;">Item 1</li>
                <li class="style-guide__box" style="padding: 0.5rem 1rem;">Item 2</li>
                <li class="style-guide__box" style="padding: 0.5rem 1rem;">Item 3</li>
                <li class="style-guide__box" style="padding: 0.5rem 1rem;">Longer Item 4</li>
            </ul>
        </div>
    </div>

    <!-- Form Inputs -->
    <div class="style-guide__subsection">
        <h3>Form Inputs</h3>
        <small><code class="style-guide__code">.input</code> <code class="style-guide__code">.select</code></small>
        <div class="style-guide__demo">
            <div style="display: grid; gap: 1rem; max-width: 400px;">
                <input type="text" class="input" placeholder=".input">
                <input type="text" class="input input--button-height" placeholder=".input--button-height">
                <select class="input select">
                    <option>.select</option>
                    <option>Option 2</option>
                    <option>Option 3</option>
                </select>
                <textarea class="input" rows="3" placeholder=".input (textarea)"></textarea>
            </div>
        </div>
    </div>

    <!-- Checkbox -->
    <div class="style-guide__subsection">
        <h3>Checkbox</h3>
        <small><code class="style-guide__code">.checkbox-field</code></small>
        <div class="style-guide__demo">
            <div style="max-width: 400px;">
                <div class="checkbox-field">
                    <input type="checkbox" id="check1" name="check1">
                    <label for="check1">Checkbox option 1</label>
                </div>
                <div class="checkbox-field">
                    <input type="checkbox" id="check2" name="check2" checked>
                    <label for="check2">Checkbox option 2 (checked)</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Radio -->
    <div class="style-guide__subsection">
        <h3>Radio</h3>
        <small><code class="style-guide__code">.radio-field</code></small>
        <div class="style-guide__demo">
            <div style="max-width: 400px;">
                <div class="radio-field">
                    <input type="radio" id="radio1" name="radiogroup" checked>
                    <label for="radio1">Radio option 1</label>
                </div>
                <div class="radio-field">
                    <input type="radio" id="radio2" name="radiogroup">
                    <label for="radio2">Radio option 2</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Blockquote -->
    <div class="style-guide__subsection">
        <h3>Blockquote</h3>
        <small><code class="style-guide__code">.blockquote</code></small>
        <div class="style-guide__demo">
            <blockquote class="blockquote">
                <p>This is a styled blockquote with a decorative left border.</p>
                <cite>— Citation</cite>
            </blockquote>
        </div>
    </div>

    <!-- Tooltip -->
    <div class="style-guide__subsection">
        <h3>Tooltip</h3>
        <small><code class="style-guide__code">.tooltip</code></small>
        <div class="style-guide__demo">
            <div style="padding-top: 1rem;">
                <span class="tooltip">This is a tooltip message with an arrow pointing up</span>
            </div>
        </div>
    </div>

    <!-- Prose -->
    <div class="style-guide__subsection">
        <h3>Prose</h3>
        <small><code class="style-guide__code">.prose</code> — adds consistent margins to content elements</small>
        <div class="style-guide__demo">
            <div class="prose" style="max-width: 500px;">
                <p>First paragraph within .prose container.</p>
                <p>Second paragraph with automatic margin spacing applied.</p>
                <ul>
                    <li>List item 1</li>
                    <li>List item 2</li>
                </ul>
                <p>Another paragraph after the list.</p>
            </div>
        </div>
    </div>

    <!-- Focus Styles -->
    <div class="style-guide__subsection">
        <h3>Focus Styles</h3>
        <small><code class="style-guide__code">.focus--1</code> <code class="style-guide__code">.focus--2</code></small>
        <div class="style-guide__demo">
            <div class="flex-list">
                <button class="btn" style="outline: 2px dotted var(--color-foreground);">.focus--1 (dotted outline)</button>
                <button class="btn" style="box-shadow: 0 0 0 2px var(--color-foreground);">.focus--2 (box-shadow)</button>
            </div>
        </div>
    </div>

    <!-- Cross Icon -->
    <div class="style-guide__subsection">
        <h3>Cross Icon</h3>
        <small><code class="style-guide__code">.cross</code></small>
        <div class="style-guide__demo">
            <div class="flex-list">
                <button class="btn btn--square cross" style="--cross--size: 50%; --cross--color: currentColor;" aria-label="Close"></button>
                <span style="width: 24px; height: 24px; display: inline-block;" class="cross" style="--cross--size: 100%;"></span>
            </div>
        </div>
    </div>

    <!-- Mask Icon -->
    <div class="style-guide__subsection">
        <h3>Mask Icon</h3>
        <small><code class="style-guide__code">.mask-icon</code> — uses currentColor + mask-image</small>
        <div class="style-guide__demo">
            <p>Apply to pseudo-elements with a mask-image to create icons that inherit text color.</p>
        </div>
    </div>

    <!-- Responsive Embed -->
    <div class="style-guide__subsection">
        <h3>Responsive Embed</h3>
        <small><code class="style-guide__code">.responsive-embed</code></small>
        <div class="style-guide__demo">
            <div class="responsive-embed" style="max-width: 400px; aspect-ratio: 16/9;">
                <div class="style-guide__placeholder" style="display: flex; align-items: center; justify-content: center;">16:9 embed</div>
            </div>
        </div>
    </div>

    <!-- Media Embed -->
    <div class="style-guide__subsection">
        <h3>Media Embed</h3>
        <small><code class="style-guide__code">.media-embed</code> — grid container with figcaption styling</small>
        <div class="style-guide__demo">
            <figure class="media-embed" style="max-width: 400px;">
                <div class="style-guide__placeholder" style="aspect-ratio: 4/3;"></div>
                <figcaption>This is a centered figcaption with .type-small styling</figcaption>
            </figure>
        </div>
    </div>
</section>

<!-- ============================================
     UTILITIES (4-utilities)
     ============================================ -->

<section class="style-guide__section">
    <h2>Utilities</h2>

    <!-- Color Context -->
    <div class="style-guide__subsection">
        <h3>Color Context</h3>
        <small><code class="style-guide__code">.color-context-{color}</code> <code class="style-guide__code">.has-{color}-background-color</code></small>
        <p style="margin: 0.5rem 0;">Sets background color, foreground color, focus color, and link colors based on the color's configuration.</p>
        <div class="style-guide__demo" style="padding: 0; overflow: hidden; border-radius: var(--border-radius--m);">
            <div class="color-context-brand-1" style="padding: 1.5rem;">
                <strong>.color-context-brand-1</strong>
                <p style="margin: 0.5rem 0 0;">Text with <a href="#">a link</a> inherits correct colors.</p>
            </div>
            <div class="color-context-brand-2" style="padding: 1.5rem;">
                <strong>.color-context-brand-2</strong>
                <p style="margin: 0.5rem 0 0;">Text with <a href="#">a link</a> inherits correct colors.</p>
            </div>
            <div class="color-context-brand-3" style="padding: 1.5rem;">
                <strong>.color-context-brand-3</strong>
                <p style="margin: 0.5rem 0 0;">Text with <a href="#">a link</a> inherits correct colors.</p>
            </div>
        </div>
    </div>

    <!-- Foreground Color -->
    <div class="style-guide__subsection">
        <h3>Foreground Color</h3>
        <small><code class="style-guide__code">.foreground-from-{color}</code></small>
        <p style="margin: 0.5rem 0;">Sets only the foreground color (text, links, focus) without changing background.</p>
        <div class="style-guide__demo">
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div class="foreground-from-brand-1 bg-brand-1">
                    <strong>.foreground-from-brand-1</strong>
                    <p style="margin: 0;">With <a href="#">a link</a></p>
                </div>
                <div class="foreground-from-blue bg-blue">
                    <strong>.foreground-from-blue</strong>
                    <p style="margin: 0;">With <a href="#">a link</a></p>
                </div>
                <div class="foreground-from-red bg-red">
                    <strong>.foreground-from-red</strong>
                    <p style="margin: 0;">With <a href="#">a link</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Width -->
    <div class="style-guide__subsection alignfull">
        <h3 class="alignwide">Content Width</h3>
        <small><code class="style-guide__code">.content-width-*</code> <code class="style-guide__code">.content-width-fluid-*</code> <code class="style-guide__code">.content-width-full</code></small>
        <div class="style-guide__demo">
            <div class="content-width-2xs style-guide__box">.content-width-2xs</div>
            <div class="content-width-xs style-guide__box" style="margin-top: 0.5rem;">.content-width-xs</div>
            <div class="content-width-sm style-guide__box" style="margin-top: 0.5rem;">.content-width-sm</div>
            <div class="content-width-md style-guide__box" style="margin-top: 0.5rem;">.content-width-md</div>
            <div class="content-width-full style-guide__box" style="margin-top: 0.5rem;">.content-width-full</div>
        </div>
    </div>

    <!-- Max Width Fluid -->
    <div class="style-guide__subsection alignfull">
        <h3 class="alignwide">Content Width Fluid</h3>
        <small><code class="style-guide__code">.content-width-fluid-*</code></small>
        <div class="style-guide__demo">
            <div class="content-width-fluid-xs style-guide__box">.content-width-fluid-xs</div>
            <div class="content-width-fluid-sm style-guide__box" style="margin-top: 0.5rem;">.content-width-fluid-sm</div>
            <div class="content-width-fluid-md style-guide__box" style="margin-top: 0.5rem;">.content-width-fluid-md</div>
            <div class="content-width-fluid-lg style-guide__box" style="margin-top: 0.5rem;">.content-width-fluid-lg</div>
        </div>
    </div>

    <!-- Alignment -->
    <div class="style-guide__subsection">
        <h3>WordPress Alignment</h3>
        <small><code class="style-guide__code">.alignleft</code> <code class="style-guide__code">.alignright</code> <code class="style-guide__code">.aligncenter</code> <code class="style-guide__code">.alignwide</code> <code class="style-guide__code">.alignfull</code></small>
        <div class="style-guide__demo" style="overflow: hidden;">
            <div class="aligncenter style-guide__box" style="max-width: 200px;">.aligncenter</div>
        </div>
        <div class="style-guide__demo" style="overflow: hidden;">
            <div class="alignleft style-guide__box" style="width: 150px;">.alignleft</div>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
            <div style="clear: both;"></div>
        </div>
        <div class="style-guide__demo" style="overflow: hidden;">
            <div class="alignright style-guide__box" style="width: 150px;">.alignright</div>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- List Reset -->
    <div class="style-guide__subsection">
        <h3>List Reset</h3>
        <small><code class="style-guide__code">.list-reset</code> <code class="style-guide__code">.list-reset-hard</code></small>
        <div class="style-guide__demo">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div>
                    <p><strong>Default:</strong></p>
                    <ul>
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
                <div>
                    <p><strong>.list-reset:</strong></p>
                    <ul class="list-reset">
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
                <div>
                    <p><strong>.list-reset-hard:</strong></p>
                    <ul class="list-reset-hard">
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Margin Trim -->
    <div class="style-guide__subsection">
        <h3>Margin Trim</h3>
        <small><code class="style-guide__code">.margin-trim</code> <code class="style-guide__code">.margin-trim-first</code> <code class="style-guide__code">.margin-trim-last</code></small>
        <div class="style-guide__demo">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="border: 2px solid var(--color-brand-1); padding: 0.5rem;">
                    <p><strong>Without .margin-trim:</strong></p>
                    <p style="margin: 1rem 0;">Paragraph with margins</p>
                    <p style="margin: 1rem 0;">Another paragraph</p>
                </div>
                <div class="margin-trim" style="border: 2px solid var(--color-brand-1); padding: 0.5rem;">
                    <p><strong>With .margin-trim:</strong></p>
                    <p style="margin: 1rem 0;">Paragraph with margins</p>
                    <p style="margin: 1rem 0;">Another paragraph</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Img Fit -->
    <div class="style-guide__subsection">
        <h3>Image Fit</h3>
        <small><code class="style-guide__code">.img-fit</code> — container for object-fit: cover images</small>
        <div class="style-guide__demo">
            <div class="img-fit" style="width: 200px; aspect-ratio: 16/9;">
                <div class="style-guide__placeholder"></div>
            </div>
        </div>
    </div>

    <!-- Screen Reader -->
    <div class="style-guide__subsection">
        <h3>Screen Reader Text</h3>
        <small><code class="style-guide__code">.screen-reader</code> <code class="style-guide__code">.sr-only</code> <code class="style-guide__code">.screen-reader-text</code></small>
        <div class="style-guide__demo">
            <p>Visually hidden but accessible to screen readers:</p>
            <button class="btn">
                Download
                <span class="sr-only">(opens PDF in new window)</span>
            </button>
            <p style="margin-top: 0.5rem;"><small>The span with .sr-only contains "(opens PDF in new window)" but is visually hidden.</small></p>
        </div>
    </div>

    <!-- No Scroll -->
    <div class="style-guide__subsection">
        <h3>No Scroll</h3>
        <small><code class="style-guide__code">.no-scroll</code> — overflow: hidden (for modals)</small>
        <div class="style-guide__demo">
            <p>Apply to body when modal is open to prevent background scrolling.</p>
        </div>
    </div>

    <!-- No Focus -->
    <div class="style-guide__subsection">
        <h3>No Focus</h3>
        <small><code class="style-guide__code">.no-focus-be-careful</code> — removes focus outline (use carefully!)</small>
        <div class="style-guide__demo">
            <p>Only use when providing alternative focus indication. Name reminds developers to be careful about accessibility.</p>
        </div>
    </div>
</section>
