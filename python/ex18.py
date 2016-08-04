
def print_two(*args):
    arg1, arg2 = args
    print "arg1: %r, arg2: %r" % (arg1, arg2)

def print_two_again(arg1, arg2):
    print "arg1: %r, arg2: %r" % (arg2, arg2)

def print_one(arg1):
    print "arg1: %r" % arg1

def print_none():
    print "nada yo"

print_two("c", "w")
print_two_again('a', 'b')
print_one('foo')
print_none()
