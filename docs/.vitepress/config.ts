import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'cakephp-databaselog',
  description: 'Store, inspect, and maintain application logs in CakePHP.',
  base: '/CakePHP-DatabaseLog/',
  cleanUrls: true,
  srcExclude: ['README.md', 'upgrading.md'],
  lastUpdated: true,
  head: [
    ['link', { rel: 'icon', href: '/CakePHP-DatabaseLog/favicon.svg', type: 'image/svg+xml' }],
  ],
  sitemap: { hostname: 'https://dereuromark.github.io/CakePHP-DatabaseLog/' },
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Installation', link: '/guide/installation' },
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'Reference', link: '/reference/configuration' },
    ],
    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Overview', link: '/guide/' },
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Getting started', link: '/guide/getting-started' },
          { text: 'Upgrading', link: '/guide/upgrading' },
          { text: 'Contributing', link: '/guide/contributing' },
        ],
      },
      {
        text: 'Reference',
        items: [
          { text: 'Configuration', link: '/reference/configuration' },
          { text: 'Commands', link: '/reference/commands' },
          { text: 'Admin interface', link: '/reference/admin' },
          { text: 'Monitoring', link: '/reference/monitoring' },
        ],
      },
    ],
    search: { provider: 'local' },
    socialLinks: [{ icon: 'github', link: 'https://github.com/dereuromark/CakePHP-DatabaseLog' }],
    editLink: {
      pattern: 'https://github.com/dereuromark/CakePHP-DatabaseLog/edit/master/docs/:path',
      text: 'Edit this page on GitHub',
    },
    footer: { message: 'Released under the MIT License.' },
  },
})
