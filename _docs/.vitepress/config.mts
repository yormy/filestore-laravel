import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "Filestore",
  description: "Storing files local and on s3 and encrypted",
  themeConfig: {
      search: {
          provider: 'local'
      },
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Examples', link: '/markdown-examples' }
    ],

    sidebar: [
        {
            text: 'Examples',
            items: [
                {text: 'Markdown Examples', link: '/markdown-examples'},
                {text: 'Runtime API Examples', link: '/api-examples'}
            ],
        },
        {
        text: 'Contributing',
          items: [
                { text: 'Report Security Issues', link: '/general/report_security' },
                { text: 'Roadmap', link: '/general/roadmap' },
                { text: 'License', link: '/general/license' },
                { text: 'Change log', link: '/general/changelog' },
                { text: 'Contributing', link: '/general/contributing' },
                { text: 'Code of Conduct', link: '/general/code_of_conduct' },
                { text: 'Credits', link: '/general/credits' },
            ]
        },

        { text: 'Contact', items: [
                { text: 'Contact', link: '/general/contact' },
                { text: 'Support', link: '/general/support/support-me' },
                { text: 'Donations', link: '/general/support/donations' },
            ]
        },
    ],

      footer: {
          message: 'Released under the MIT License.',
          copyright: 'Copyright © 2022 to present Yormy'
      },
      socialLinks: [
          { icon: 'github', link: 'https://github.com/yormy/files-laravel' }
      ]
  }
})

