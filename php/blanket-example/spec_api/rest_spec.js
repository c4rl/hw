var chakram = require('chakram'), expect = chakram.expect;

var URL = 'http://blanket-example.localhost:8080';

describe('Muppets errwhere', function() {

  it('got a kermit', function () {
    var response = chakram.get(URL + '/muppets/1');
    expect(response).to.have.status(200);
    expect(response).to.have.header('content-type', 'application/json');
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
    expect(response).to.have.header('content-type', 'application/json');
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
    expect(response).to.have.header('content-type', 'application/json');
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

    return chakram.post(URL + '/muppets', data)
      .then(function(postResponse) {
        expect(postResponse).to.have.status(200);
        expect(postResponse).to.have.header('content-type', 'application/json');
        expect(postResponse).to.comprise.of.json({
          id: 3,
          name: 'grover',
          occupation: 'blue guy'
        });
        return chakram.get(URL + '/muppets/3');
      })
      .then(function(getResponse) {
        expect(getResponse).to.have.status(200);
        expect(getResponse).to.have.header('content-type', 'application/json');
        expect(getResponse).to.comprise.of.json({
          id: 3,
          name: 'grover',
          occupation: 'blue guy'
        });
      })
  });

  it('updates teh mupp', function() {
    var data = {
        name: 'cookie monster'
    };

    return chakram.put(URL + '/muppets/3', data)
      .then(function(putResponse) {
        expect(putResponse).to.have.status(200);
        expect(putResponse).to.have.header('content-type', 'application/json');
        expect(putResponse).to.comprise.of.json({
            id: 3,
            name: 'cookie monster',
            occupation: 'blue guy'
        });
        return chakram.get(URL + '/muppets/3');
      })
      .then(function(getResponse) {
        expect(getResponse).to.have.status(200);
        expect(getResponse).to.have.header('content-type', 'application/json');
        expect(getResponse).to.comprise.of.json({
          id: 3,
          name: 'cookie monster',
          occupation: 'blue guy'
        });
      });
  });

  it('deletes teh mupp', function() {
    return chakram.delete(URL + '/muppets/3')
      .then(function(deleteResponse) {
        expect(deleteResponse).to.have.status(200);
        expect(deleteResponse).to.have.header('content-type', 'application/json');
        expect(deleteResponse).to.comprise.of.json({
            id: 3,
            name: 'cookie monster',
            occupation: 'blue guy'
        });
        return chakram.get(URL + '/muppets/3');
      })
      .then(function(getResponse) {
        expect(getResponse).to.have.status(404);
        expect(getResponse).to.have.header('content-type', 'text/html;charset=UTF-8');
        expect(getResponse.body).to.equal('HTTP/1.1 404 Not Found');
      });
  });

});
