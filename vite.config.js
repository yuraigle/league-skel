import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'node:path'

export default defineConfig({

    plugins: [
        vue(),
    ],

    // config
    root: 'src',
    base: '/dist/',

    build: {
        outDir: resolve(__dirname, 'public/dist'),
        emptyOutDir: true,
        manifest: true,

        rollupOptions: {
            input: resolve(__dirname, 'src/main.js'),
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor'
                    }
                },
            },
        }
    },

    server: {
        strictPort: true,
        port: 5133,
        cors: {
            origin: 'http://localhost:8000',
        }
    },

    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js'
        }
    }
})
