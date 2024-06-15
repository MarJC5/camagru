const escapeHTML = (str, allowTags = false) => {
    if (typeof str === "number") {

      str = str.toString();
    }

    if (typeof str !== "string") {
      console.error("escapeHTML: Argument is not a string.");
      return "";
    }

    if (allowTags) {
      // Allow certain HTML tags
      const allowedTags = {
        b: [],
        i: [],
        u: [],
        s: [],
        a: ["href", "title"],
        code: [],
        pre: [],
        blockquote: [],
        ul: [],
        ol: [],
        li: [],
        h1: [],
        h2: [],
        h3: [],
        h4: [],
        h5: [],
        h6: [],
        p: [],
        br: [],
        hr: [],
        table: [],
        thead: [],
        tbody: [],
        tfoot: [],
        tr: [],
        th: [],
        td: [],
        div: [],
        span: [],
      };

      // Regex to match allowed tags and their attributes
      const tagRegex = /<\/?([a-zA-Z]+)([^>]*)>/g;
      const attrRegex = /([a-zA-Z]+)="([^"]*)"/g;

      return str.replace(tagRegex, (fullTag, tagName, attrs) => {
        if (allowedTags[tagName]) {
          // Allow only specified attributes
          let safeAttrs = "";
          let match;
          while ((match = attrRegex.exec(attrs)) !== null) {
            const attrName = match[1];
            const attrValue = match[2];
            if (allowedTags[tagName].includes(attrName)) {
              safeAttrs += ` ${attrName}="${Target.escapeHTML(attrValue)}"`;
            }
          }
          return `<${
            fullTag.startsWith("</") ? "/" : ""
          }${tagName}${safeAttrs}>`;
        } else {
          // Escape the whole tag if it's not allowed
          return fullTag.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }
      });
    } else {
      // Escape all HTML tags
      return str.replace(
        /[&<>"']/g,
        (tag) =>
          ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;",
          }[tag])
      );
    }
};

export { escapeHTML };
  