var chakram = require('chakram'), expect = chakram.expect;

var URL = 'http://muppets.localhost:8080';

describe('Muppets errwhere', function() {

  it('got a kermit', function () {
    var response = chakram.get(URL + '/muppets/1');
    expect(response).to.have.status(200);
    expect(response).to.comprise.of.json({
      id: 1,
      name: 'kermit',
      occupation: 'the frog'
    });
    return chakram.wait();
  });

  it('got a mp', function () {
    var response = chakram.get(URL + '/muppets/2');
    expect(response).to.have.status(200);
    expect(response).to.comprise.of.json({
      id: 2,
      name: 'miss piggy',
      occupation: 'frog bae'
    });
    return chakram.wait();
  });

  it('got teh mupps', function () {
    var response = chakram.get(URL + '/muppets');
    expect(response).to.have.status(200);
    expect(response).to.comprise.of.json({
      total: 2,
      page: 1,
      per_page: 10,
      muppets: [
        {
          id: 1,
          name: 'kermit',
          occupation: 'the frog'
        },
        {
          id: 2,
          name: 'miss piggy',
          occupation: 'frog bae'
        }
      ]
    });
    return chakram.wait();
  });

  it('creates teh mupp', function () {
    var data = {
      name: 'grover',
      occupation: 'blue guy'
    };
    var response = chakram.post(URL + '/muppets', data);

    expect(response).to.have.status(200);
    expect(response).to.comprise.of.json({
      id: 3,
      name: 'grover',
      occupation: 'blue guy'
    });

    return chakram.wait();
  });

  it('updates teh mupp', function() {
      var data = {
          name: 'cookie monster'
      };
      var response = chakram.put(URL + '/muppets/3', data);

      expect(response).to.have.status(200);
      expect(response).to.comprise.of.json({
          id: 3,
          name: 'cookie monster',
          occupation: 'blue guy'
      });

      return chakram.wait();
  });

  it('deletes teh mupp', function() {
      var response = chakram.delete(URL + '/muppets/3');

      expect(response).to.have.status(200);
      expect(response).to.comprise.of.json({
          id: 3,
          name: 'cookie monster',
          occupation: 'blue guy'
      });

      return chakram.wait();
  });

});
