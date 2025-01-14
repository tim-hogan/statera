// JavaScript source code
var st = {
	def: function (x) {
		if (typeof x != 'undefined')
			return true;
		return false;
	},
	ge: function (t) {
		return document.getElementById(t);
	},
	ga: function (n, a) {
		return n.getAttribute(a);
	},
	sa: function (n, a , v) {
		n.setAttribute(a,v);
	},
	ce: function (t) {
		return document.createElement(t);
	},
	cea: function (t, p) {
		var e = st.ce(t);
		p.appendChild(e);
		return e;
	},
	ci: function (n, v, f) {
		let i = st.cea("input", f);
		i.type = "text";
		i.name = n;
		i.value = v;
		return i;
	},
	cisz: function (t,n,v,sz) {
		let i = st.ce("input");
		i.type = t;
		i.name = n;
		if (st.def(v))
			i.value = v;
		if (st.def(sz))
			i.setAttribute("size", sz);
		return i;
	},
	removeAllChildren: function (n) {
		while (n.firstChild) {
			n.removeChild(n.firstChild);
		}
	},
	//table
	trow: function (tbl, ...a) {
		let tr = st.cea("TR", tbl)
		let i = 0;
		for (let c of a) {
			let td = st.cea("TD", tr);
			td.innerHTML = c;
			td.className = "td" + i++;
		}
	},
	otrow: function (tbl, ...a) {
		let tr = st.cea("TR", tbl)
		let i = 0;
		for (let c of a) {
			let td = st.cea("TD", tr);
			td.appendChild(c);
			td.className = "td" + i++;
		}
	}

}
