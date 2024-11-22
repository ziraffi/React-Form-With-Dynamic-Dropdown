import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ command }) => {
  const commonConfig = {
    plugins: [react()],
    esbuild: {
      loader: 'jsx',
      include: /src\/.*\.jsx?$/,
      exclude: [],
    },
    optimizeDeps: {
      esbuildOptions: {
        loader: {
          '.js': 'jsx',
        },
      },
    },
  };

  if (command === 'serve') {
    // Development-specific config
    return {
      ...commonConfig,
      server: {
        open: true,
        port: 3000,
        host: true,
      },
      build: {
        outDir: 'dist',
        rollupOptions: {
          input: 'widgets/formReact/src/index.jsx',
        },
      },
    };
  } else {
    // Production-specific config
    return {
      ...commonConfig,
      build: {
        outDir: 'dist',
        rollupOptions: {
          input: 'widgets/formReact/src/index.jsx',
          output: {
            entryFileNames: 'bundle.js',
          },
        },
      },
    };
  }
});