
#--------------------------------------------------------------------------
# Sass
#--------------------------------------------------------------------------

guard 'sass',
    :style => :compressed,
    :input => 'public/css',
    :output => 'public/css'


#--------------------------------------------------------------------------
# Javascript
#--------------------------------------------------------------------------

guard :concat,
    type: 'js',
    files: %w(jquery jquery.datepicker jquery.fancybox carousel script),
    input_dir: 'public/javascript',
    output: 'public/javascript/all'

guard 'uglify', :destination_file => 'public/javascript/all.js' do
    watch (%r{public/javascript/script.js})
end
