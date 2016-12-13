var path = require('path');
var args = require('yargs').argv;
var BaseDir = path.join(__dirname,'../'),
    InstallationDir = path.join(__dirname,args.installationDir || '../../');


var Config = {
    BaseDir : BaseDir,
    InstallationDir : InstallationDir,
    appDir : path.join(BaseDir,'resources/assets/app/'),
    publicDir : path.join(BaseDir,'resources/public/'),
    optimizedDirJs : path.join(BaseDir,'resources/public/js/**/*.js'),
    optimizedDirCss : path.join(BaseDir,'resources/public/css/**/*.css'),
    templatesDir : path.join(BaseDir,'resources/assets/app/templates/**/*.html'),
    cssDir : path.join(BaseDir,'resources/assets/css/**/*.css'),
    imgDir : path.join(BaseDir,'resources/assets/img/**/*.css'),
    jsDir : path.join(BaseDir,'resources/assets/js/**/*.js'),
    publicDirJs : path.join(InstallationDir,'public/vendor/mcms/products/js'),
    publicDirCss : path.join(InstallationDir,'public/vendor/mcms/products/css'),
    publicDirImg : path.join(InstallationDir,'public/vendor/mcms/products/img'),
    publicDirTemplates : path.join(InstallationDir,'public/vendor/mcms/products/app/templates')
};

module.exports = Config;