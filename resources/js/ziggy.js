const Ziggy = {"url":"http:\/\/localhost\/komang\/public","port":null,"defaults":{},"routes":{"login":{"uri":"login","methods":["GET","HEAD"]},"login.store":{"uri":"login","methods":["POST"]},"logout":{"uri":"logout","methods":["POST"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"pelanggan.index":{"uri":"pelanggan","methods":["GET","HEAD"]},"pelanggan.show":{"uri":"pelanggan\/{master}","methods":["GET","HEAD"],"parameters":["master"],"bindings":{"master":"id"}},"kddk.show":{"uri":"kddk\/{kode}","methods":["GET","HEAD"],"parameters":["kode"]},"admin.users.index":{"uri":"admin\/users","methods":["GET","HEAD"]},"admin.users.store":{"uri":"admin\/users","methods":["POST"]},"admin.users.destroy":{"uri":"admin\/users\/{user}","methods":["DELETE"],"parameters":["user"],"bindings":{"user":"id"}},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]},"storage.local.upload":{"uri":"storage\/{path}","methods":["PUT"],"wheres":{"path":".*"},"parameters":["path"]}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
