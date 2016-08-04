def cheese_and_crackers(cheese_count, boxes_of_crackers):
    print "You have %d cheeses!" % cheese_count
    print "You have %d boxes of crackers!" % boxes_of_crackers
    print "Man that's enough for a party!"
    print "Get a room\n"

print "We can just giv the funciton numbers directly."
cheese_and_crackers(20, 30)

print "Or we can use vars"
amount_of_cheese = 10
amount_of_crackers = 50
cheese_and_crackers(amount_of_cheese, amount_of_crackers)

print "We can even do expressions"
cheese_and_crackers(6+4, 50-2)

print "We can even do expressions with variables"
cheese_and_crackers(1+amount_of_cheese, 1+amount_of_crackers)
