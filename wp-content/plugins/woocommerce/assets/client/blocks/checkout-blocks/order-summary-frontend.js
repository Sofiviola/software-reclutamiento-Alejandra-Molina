"use strict";(self.webpackChunkwebpackWcBlocksCartCheckoutFrontendJsonp=self.webpackChunkwebpackWcBlocksCartCheckoutFrontendJsonp||[]).push([[12],{9156:(e,t,c)=>{c.r(t),c.d(t,{default:()=>y});var r=c(1609),n=c(5738),a=c(910),o=c(8509),l=c(7723),m=c(7104),s=c(9813),u=c(224),i=c(6087),d=c(851),k=c(1360),p=c(2592),E=c(7661),h=c(4656);const w=({children:e,stepHeadingContent:t})=>(0,r.createElement)("div",{className:"wc-block-components-checkout-step__heading"},(0,r.createElement)(h.Title,{"aria-hidden":"true",className:"wc-block-components-checkout-step__title",headingLevel:"2"},e),!!t&&(0,r.createElement)("span",{className:"wc-block-components-checkout-step__heading-content"},t)),y=({children:e,className:t=""})=>{const{cartTotals:c}=(0,o.V)(),{isLarge:h}=(0,p.G)(),[y,_]=(0,i.useState)(!1),b=(0,a.getCurrencyFromPriceResponse)(c),v=parseInt(c.total_price,10),N=(0,i.useId)(),g=h?{}:{role:"button",onClick:()=>_(!y),"aria-expanded":y,"aria-controls":N,tabIndex:0,onKeyDown:e=>{"Enter"!==e.key&&" "!==e.key||_(!y)}};return(0,r.createElement)(r.Fragment,null,(0,r.createElement)("div",{className:t},(0,r.createElement)("div",{className:(0,d.A)("wc-block-components-checkout-order-summary__title",{"is-open":y}),...g},(0,r.createElement)("p",{className:"wc-block-components-checkout-order-summary__title-text",role:"heading"},(0,l.__)("Order summary","woocommerce")),!h&&(0,r.createElement)(r.Fragment,null,(0,r.createElement)(E.FormattedMonetaryAmount,{currency:b,value:v}),(0,r.createElement)(m.A,{className:"wc-block-components-checkout-order-summary__title-icon",icon:y?s.A:u.A}))),(0,r.createElement)("div",{className:(0,d.A)("wc-block-components-checkout-order-summary__content",{"is-open":y}),id:N},e,(0,r.createElement)("div",{className:"wc-block-components-totals-wrapper"},(0,r.createElement)(n.Ay,{currency:b,values:c})),(0,r.createElement)(k.Xm,null))),!h&&(0,r.createElement)(k.iG,null,(0,r.createElement)("div",{className:`${t} checkout-order-summary-block-fill-wrapper`},(0,r.createElement)(w,null,(0,r.createElement)(r.Fragment,null,(0,l.__)("Order summary","woocommerce"))),(0,r.createElement)("div",{className:"checkout-order-summary-block-fill"},e,(0,r.createElement)("div",{className:"wc-block-components-totals-wrapper"},(0,r.createElement)(n.Ay,{currency:b,values:c})),(0,r.createElement)(k.Xm,null)))))}},1360:(e,t,c)=>{c.d(t,{VM:()=>m,Xm:()=>o,iG:()=>l});var r=c(1609),n=c(1e3),a=c(8509);const o=()=>{const{extensions:e,receiveCart:t,...c}=(0,a.V)(),o={extensions:e,cart:c,context:"woocommerce/checkout"};return(0,r.createElement)(n.ExperimentalOrderMeta.Slot,{...o})},{Fill:l,Slot:m}=(0,n.createSlotFill)("checkoutOrderSummaryActionArea")}}]);