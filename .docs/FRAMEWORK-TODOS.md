From HB build
- [ ] Simplify type styles and type variables. Remove unnecessary ones.
- [ ] Move <a> styling out of type-styles
@theme {
    --font-pt-serif: "PT Serif", Georgia, serif;
    --font-national: "National", system-ui, sans-serif;
}
        --font-base-weight: 400;
        --font-base-bold: 700;


- [x] test all blocks in editor
- [x] updated site-header from cog
- [x] Add @layer component to all component styles (or maybe not - maybe leave all components unlayered (or change layer order))
- [x] Write documentation for how to use tailwind and @layers.
- [x] clean up css patterns vs utilities
- [x] rename .grid to grid-simple, --grid--columns to --cols
- [ ] add text-underline-offset globally and as a global variable
- [x] Migrate to classes() from buildClasses() (<header class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>)
- [ ] rename all acf-json field groups
- [ ] Sort out mask-icon
