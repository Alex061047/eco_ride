export default class Route {
  constructor(url, title, pathHtml, authorize, pathJS = []) {
    this.url = url;
    this.title = title;
    this.pathHtml = pathHtml;
    this.pathJS = Array.isArray(pathJS) ? pathJS : [pathJS]; 
    this.authorize = authorize;
  }
}
