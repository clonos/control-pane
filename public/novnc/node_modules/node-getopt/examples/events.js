require('..').create([
    ['a', 'add=+'],
    ['r', 'remove=+']
]).on('a', function(v) {
    console.log('add', v)
}).on('remove', function(v) {
    console.log('remove', v)
}).parse([
    '-a', 'a1',
    '-r', 'r1',
    '-a', 'a2'
])
/*
EXPECTED OUTPUT
add a1
remove r1
add a2
*/
