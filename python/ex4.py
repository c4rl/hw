cars = 100
drivers = 20
passengers = 500
space_in_car = 4.0

cars_not_drivn = cars - drivers
cars_driven = drivers
passengers_driven = (space_in_car - 1) * cars

print "There are", passengers_driven, "passengers driven"
