using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using PHCDto.DTO;

namespace PetHealthClub.Controllers
{
    public class BaseController : Controller
    {
        //
        // GET: /Base/


        protected override void OnActionExecuting(ActionExecutingContext filterContext)
        {
            if (Session["User"] != null)
            {
                UDTO = (FbDTO)Session[HomeController.USER];
                base.OnActionExecuting(filterContext);
            }
            else
            {
                //TODO: Find a better way of getting the session?
                filterContext.Result = Redirect( Url.Content("~/Home/index") );
            }
        }
        public FbDTO UDTO { get; set; }
    }
}
