all: lib/getopt.js test/test.js

lib/%.js: lib/%.toffee
	toffee -c -o lib $<

test/%.js: test/%.toffee
	toffee -c -o test $<

bin/%.js: bin/%.toffee
	toffee -c -o bin $<

test-all:
	node test/test.js
	node examples/help.js
	node examples/simple.js -h
