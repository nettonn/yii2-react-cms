const { createProxyMiddleware } = require("http-proxy-middleware");
// const php = require("php-proxy-middleware");

const TIMEOUT = 3 * 1000;

module.exports = function (app) {
  app.use(
    createProxyMiddleware("/admin-api", {
      target: process.env.REACT_APP_HOST,
      logLevel: "debug",
      changeOrigin: true,
      secure: process.env.REACT_APP_HOST_SECURE,
      timeout: TIMEOUT,
      xfwd: true,
      // proxyTimeout: 1000,
      headers: {
        Connection: "keep-alive",
      },
    })
  );
  app.use(
    createProxyMiddleware("/files", {
      target: process.env.REACT_APP_HOST,
      logLevel: "debug",
      changeOrigin: true,
      secure: process.env.REACT_APP_HOST_SECURE,
      timeout: TIMEOUT,
      xfwd: true,
      // proxyTimeout: 1000,
      headers: {
        Connection: "keep-alive",
      },
    })
  );
  // app.use(
  //   "/admin-api",
  //   php({
  //     root: __dirname + "/../../public_html",
  //     prefix: "/api",
  //     router: __dirname + "/../../public_html/.router.php",
  //   })
  // );
  // app.use(
  //   "/files",
  //   php({
  //     root: __dirname + "/../../public_html",
  //     prefix: "/files",
  //     router: __dirname + "/../../public_html/.router.php",
  //   })
  // );
};
