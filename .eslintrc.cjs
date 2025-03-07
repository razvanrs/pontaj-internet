module.exports = {
    env: {
        browser: true,
        es2021: true,
    },
    extends: [
        'standard',
        'plugin:vue/vue3-essential',
    ],
    overrides: [
        {
            env: {
                node: true,
            },
            files: [
                '.eslintrc.{js,cjs}',
            ],
            parserOptions: {
                sourceType: 'script',
            },
        },
    ],
    parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
    },
    plugins: [
        'vue',
    ],
    rules: {
        indent: ['error', 4],
        quotes: ['warn', 'single'],
        semi: ['warn', 'never'],
        'comma-dangle': ['warn', 'always-multiline'],
        'vue/max-attributes-per-line': 0,
        'vue/require-default-prop': 0,
        'vue/singleline-html-element-content-newline': 0,
        'vue/html-indent': ['error', 4, {
            attribute: 1,
            baseIndent: 1,
            closeBracket: 0,
            alignAttributesVertically: true,
            ignores: [],
        }],
    },
}
