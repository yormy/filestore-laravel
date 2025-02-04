import { defineConfig } from 'vitepress'

export default defineConfig({
    title: "filestore",
    description: "",
    base: '/filestore/',
    head: [
        ['link', { rel: "apple-touch-icon", sizes: "180x180", href: "/assets/images/apple-touch-icon.png"}],
        ['link', { rel: "icon", type: "image/png", sizes: "32x32", href: "/assets/images/favicon-32x32.png"}],
        ['link', { rel: "icon", type: "image/png", sizes: "16x16", href: "/assets/images/favicon-16x16.png"}],
    ],
    themeConfig: {
        search: {
          provider: 'local'
        },
        nav: [
          { text: 'Home', link: '/' },
          { text: 'Guide', link: '/v1/introduction/what-is-filestore' },
        ],

        sidebar: [
            {
                text: 'Introduction',
                items: [
                    { text: 'What is Filestore', link: '/v1/introduction/what-is-filestore' },
                    { text: 'Installation', link: '/v1/introduction/installation' },
                    { text: 'Setup', link: '/v1/introduction/setup' },
                ]
            },
            {
                text: 'Contributing', items: [
                    { text: 'Frontend', link: '/v1/introduction/frontend' },
                    { text: 'Roadmap', link: '/general/roadmap' },
                    { text: 'License', link: '/general/license' },
                    { text: 'Change log', link: '/general/changelog' },
                    { text: 'Contributing', link: '/general/contributing' },
                    { text: 'Code of Conduct', link: '/general/code_of_conduct' },
                    { text: 'Credits', link: '/general/credits' },
                ]
            },
            {
              text: 'Contact',
                items: [
                    { text: 'Contact', link: '/general/contact' },
                ]
            },
        ],

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2022 to present Yormy'
        },
        socialLinks: [
            { icon: 'github', link: 'https://github.com/yormy/filestore' }
        ]
      }

})
