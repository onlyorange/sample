using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCData.Data;
using PHCDto.DTO;
using PHCRepos.Repos.Core;
using PHCRepos.Repos.AbstractContracts;
using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;
using Autofac;
using Autofac.Integration.Mvc;

namespace PHCBoot.Boot
{
    public static class Start
    {
        public static ContainerBuilder init(ContainerBuilder builder)
        {

            //Register the Repos , service and Data Files
             builder.RegisterType<PHCEntities>().InstancePerHttpRequest();

             builder.RegisterType<PetProfileDbProc>().As<IPetProfileDbProc>().InstancePerHttpRequest();
             builder.RegisterType<PetProfilePres>().As<IPetProfilePres>().InstancePerHttpRequest();

             builder.RegisterType<FbprofileDbProc>().As<IFbprofileDbProc>().InstancePerHttpRequest();
             builder.RegisterType<FbprofilePres>().As<IFbprofilePres>().InstancePerHttpRequest();

             builder.RegisterType<ChallengeDbProc>().As<IChallengeDbProc>().InstancePerHttpRequest();
             builder.RegisterType<ChallengePres>().As<IChallengePres>().InstancePerHttpRequest();

             builder.RegisterType<RedemptionRepository>().As<IRedemptionRepository>().InstancePerHttpRequest();
             builder.RegisterType<RedemptionService>().As<IRedemptionService>().InstancePerHttpRequest();

            return builder;
        }
    }
}
