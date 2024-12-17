// vite.config.js
import { defineConfig } from "file:///C:/Users/jhoha/OneDrive/Documents/laravel-v-i/phms/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/jhoha/OneDrive/Documents/laravel-v-i/phms/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/Users/jhoha/OneDrive/Documents/laravel-v-i/phms/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import Components from "file:///C:/Users/jhoha/OneDrive/Documents/laravel-v-i/phms/node_modules/unplugin-vue-components/dist/vite.js";
import { PrimeVueResolver } from "file:///C:/Users/jhoha/OneDrive/Documents/laravel-v-i/phms/node_modules/@primevue/auto-import-resolver/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true
    }),
    Components({
      resolvers: [
        PrimeVueResolver()
      ]
    })
  ],
  assetsInclude: ["**/*.png", "**/*.jpg", "**/*.svg"]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxqaG9oYVxcXFxPbmVEcml2ZVxcXFxEb2N1bWVudHNcXFxcbGFyYXZlbC12LWlcXFxccGhtc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcamhvaGFcXFxcT25lRHJpdmVcXFxcRG9jdW1lbnRzXFxcXGxhcmF2ZWwtdi1pXFxcXHBobXNcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL2pob2hhL09uZURyaXZlL0RvY3VtZW50cy9sYXJhdmVsLXYtaS9waG1zL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcbmltcG9ydCBDb21wb25lbnRzIGZyb20gJ3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3ZpdGUnO1xuaW1wb3J0IHtQcmltZVZ1ZVJlc29sdmVyfSBmcm9tICdAcHJpbWV2dWUvYXV0by1pbXBvcnQtcmVzb2x2ZXInO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgdnVlKCksXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFsncmVzb3VyY2VzL2Nzcy9hcHAuY3NzJywgJ3Jlc291cmNlcy9qcy9hcHAuanMnXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuICAgICAgICBDb21wb25lbnRzKHtcbiAgICAgICAgICAgIHJlc29sdmVyczogW1xuICAgICAgICAgICAgICBQcmltZVZ1ZVJlc29sdmVyKClcbiAgICAgICAgICAgIF1cbiAgICAgICAgfSksXG4gICAgXSxcbiAgICBhc3NldHNJbmNsdWRlOiBbJyoqLyoucG5nJywgJyoqLyouanBnJywgJyoqLyouc3ZnJ10sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBc1YsU0FBUyxvQkFBb0I7QUFDblgsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sU0FBUztBQUNoQixPQUFPLGdCQUFnQjtBQUN2QixTQUFRLHdCQUF1QjtBQUUvQixJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUN4QixTQUFTO0FBQUEsSUFDTCxJQUFJO0FBQUEsSUFDSixRQUFRO0FBQUEsTUFDSixPQUFPLENBQUMseUJBQXlCLHFCQUFxQjtBQUFBLE1BQ3RELFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNQLFdBQVc7QUFBQSxRQUNULGlCQUFpQjtBQUFBLE1BQ25CO0FBQUEsSUFDSixDQUFDO0FBQUEsRUFDTDtBQUFBLEVBQ0EsZUFBZSxDQUFDLFlBQVksWUFBWSxVQUFVO0FBQ3RELENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
