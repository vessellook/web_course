.PHONY: generate/key-pair


generate/key-pair:
	# see https://gist.github.com/ygotthilf/baa58da5c3dd1f69fae9#gistcomment-2932501
	openssl genpkey -algorithm rsa -out etc/jwtRS256.key
	openssl rsa -in etc/jwtRS256.key -pubout -out etc/jwtRS256.key.pub
	chmod 644 etc/jwtRS256.key
