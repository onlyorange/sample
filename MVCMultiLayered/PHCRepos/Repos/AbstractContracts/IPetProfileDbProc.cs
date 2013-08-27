using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCDto.DTO;
using PHCData.Data;

namespace PHCRepos.Repos.AbstractContracts
{
   public abstract class IPetProfileDbProc
    {
       public abstract PetProfileDTO SavePetProf(PetProfileDTO dto);

       public abstract PetProfileDTO GetPetDetails(int PID);

       public abstract int Update_petProfile(ProfileDTO pdto);


       public abstract ProfileDTO GetLastPetReg(int uid);


       public abstract int SaveFirstBadge(int uid,int pid);


       public abstract List<ChallengeDTO> ListChallenge();


       public abstract ChallengeDTO GetChallenginfo(int id);


       public abstract List<ChallengeDTO> GetPetnames(int uid,int cid);

       public abstract void SaveChallengeBadge(ChallengeDTO dt);


       public abstract ChallengeDTO Chkpetbadge(int pid,int cid);


       public abstract ChallengeDTO GetBadges(int uid);


       public abstract ProfileDTO GetBadgeCount(int uid);


       public abstract ChallengeDTO CompleteCh(int ebid);


       public abstract int chkuser(int uid);


       public abstract PetProfileDTO SaveFirstPet(ProfileDTO dto);
       
       public abstract bool UserHasBadge(int uid, int bid);

       public abstract void UpdateFBStatus(int ebid);


       public abstract ProfileDTO GetRewards(int uid);


       public abstract ChallengeDTO chkchallinfo(int petid, int chid);


       public abstract ChallengeDTO PreviousChallenges();


       public abstract int savepetprofile(ProfileDTO pdto);



       public abstract string Delete_pet(int id);
       public abstract ProfileDTO geteditpetprofile(int id);



       public abstract ChallengeDTO petCompleteCh(int ebid);


       public abstract ChallengeDTO InCompleteCh(int bid);


       public abstract ChallengeDTO CHNotify(int uid);


       public abstract int ChUserCnt(int cid, int uid);


       public abstract ChallengeDTO CHDExists(int chid);


       public abstract void PinUpdate(int ebid);


       public abstract void TweetUpdate(int eid);


       public abstract RewardsDTO GetWallPaperList();


       public abstract string UpdateRewards(int uid,int wid);


       public abstract ChallengeDTO GetEarnedBadgeByChallenge(int challengeId, int userId);

       
       public abstract bool AssignBadge(int userId, int badgeId, int? petId);
    }
}
