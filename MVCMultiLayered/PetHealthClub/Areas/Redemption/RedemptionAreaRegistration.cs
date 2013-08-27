using System.Web.Mvc;

namespace PetHealthClub.Areas.Redemption
{
    public class RedemptionAreaRegistration : AreaRegistration
    {
        public override string AreaName
        {
            get
            {
                return "Redemption";
            }
        }

        public override void RegisterArea(AreaRegistrationContext context)
        {
            context.MapRoute(
                "Redemption_default",
                "Redemption/{controller}/{action}/{id}",
                new { action = "Index", id = UrlParameter.Optional }
            );
        }
    }
}
