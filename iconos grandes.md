# Documento convertido por PDF2MD AI

> **Páginas:** 2  
> **OCR utilizado:** 0 páginas  
> **Palabras totales:** 270

---

## Página 1

Sí, ya se identifica claramente el problema en la captura. Ese icono de retroceso no
debería tener ese tamaño. Parece que una regla CSS está aplicando dimensiones
enormes a un SVG o a un icono de navegación.
El problema no está en el diseño del sidebar; está específicamente en el componente
de retroceso/paginación de la página.
Corrección inmediata
Si estás usando Lucide, por ejemplo, una regla global como esta puede ser la causante:
svg {
width: 100%;
height: 100%;
}
o alguna regla equivalente que esté heredando el tamaño del contenedor.
No recomiendo corregirlo poniendo simplemente svg { width:18px }, porque eso
podría romper otros iconos del sistema.
En el botón/enlace de retroceso debería quedar algo parecido a:
.back-button svg,
.btn-back svg,
.pagination svg {
width: 16px !important;
height: 16px !important;
}
Si es solamente la flecha de navegación:
.back-button {
display: inline-flex;
align-items: center;
justify-content: center;
}
.back-button svg {
width: 18px !important;
height: 18px !important;
flex: 0 0 18px;
}
Pero quiero hacerlo correctamente
Como ya estamos definiendo el Theme de CPIMuni, no quiero que hagamos un
parche aislado.
Necesitamos localizar la regla que está provocando esto. Para eso necesito revisar el
Blade de esta página y el CSS que utiliza el layout.
La URL de la captura es:


---

## Página 2

/catalogos/estructura-organizacional
Por tanto, necesito encontrar principalmente:
resources/views/
├── layouts/
│ └── ...
├── catalogos/
│ └── estructura-organizacional/
│ └── ...
└── ...
y los CSS asociados.
Adjunta el archivo Blade de estructura-organizacional y el CSS principal de
CPIMuni. Con eso puedo señalarte exactamente qué regla está haciendo gigante la
flecha y qué debemos cambiar, evitando afectar los demás iconos.