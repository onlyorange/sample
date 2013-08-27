using System.Web.Mvc;

namespace PetHealthClub.Areas.PetUser
{
    public class PetUserAreaRegistration : AreaRegistration
    {
        public override string AreaName
        {
            get
            {
                return "PetUser";
            }
        }

        public override void RegisterArea(AreaRegistrationContext context)
        {
            context.MapRoute(
                "PetUser_default",
                "PetUser/{controller}/{action}/{id}",
                new { action = "Index", id = UrlParameter.Optional }
            );
        }
    }
}
