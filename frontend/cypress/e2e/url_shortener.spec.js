describe('URL Shortener', () => {
  beforeEach(() => {
    cy.visit('/');
  });

  it('should render the form correctly', () => {
    cy.contains('h1', 'My Awesome URL Shortener');
    cy.get('input[type="url"]').should('exist');
    cy.contains('button', 'Shorten URL');
  });

  it('should shorten a URL and display it', () => {
    // Intercept the POST request
    cy.intercept('POST', '**/shorten').as('shortenUrl');
    
    cy.get('input[type="url"]').type('https://example.com');
    cy.contains('button', 'Shorten URL').click();
    
    cy.wait('@shortenUrl').then((interception) => {
      const { shortUrl } = interception.response.body;
      cy.log('Shortened URL:', shortUrl);

      cy.get('.result').should('contain', shortUrl);
      cy.get('.result a').should('have.attr', 'href', shortUrl);
      cy.contains('button', 'Copy to Clipboard').should('exist');
    });
  });

  it('should copy the shortened URL to clipboard', () => {
    // Intercept the POST request
    cy.intercept('POST', '**/shorten').as('shortenUrl');
    
    cy.get('input[type="url"]').type('https://example.com');
    cy.contains('button', 'Shorten URL').click();
    
    cy.wait('@shortenUrl').then((interception) => {
      const { shortUrl } = interception.response.body;
      cy.log('Shortened URL:', shortUrl);

      cy.get('.result').should('contain', shortUrl);
      cy.get('.result a').should('have.attr', 'href', shortUrl);
      cy.contains('button', 'Copy to Clipboard').click();

      cy.window().then((win) => {
        win.navigator.clipboard.readText().then((text) => {
          expect(text).to.include(shortUrl);
        });
      });
    });
  });
});

