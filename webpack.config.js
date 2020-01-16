const path = require('path');

module.exports = (env, argv) => {
  const { mode } = argv;

  return {
    devtool: 'development' === mode
      ? 'cheap-module-eval-source-map'
      : 'source-map',
    entry: {
      pluginSidebar: './assets/js/pluginsidebar/index.js',
    },
    module: {
      rules: [
        {
          exclude: /node_modules/,
          test: /.js$/,
          use: [
            'babel-loader',
            'eslint-loader',
          ],
        },
      ],
    },
    output: {
      filename: '[name].js',
      path: path.join(__dirname, 'build'),
    },
  };
};
